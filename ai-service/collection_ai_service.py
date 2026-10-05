import hashlib
import json
import math
import os
import re
import tempfile
import calendar
import threading
from datetime import date, datetime, timedelta
from pathlib import Path

import joblib
import numpy as np
from sklearn.ensemble import RandomForestRegressor


class CollectionAIService:
    MODEL_VERSION = 2
    MAX_INPUT_ROWS = 5000
    MAX_HISTORY_DAYS = 1825
    MAX_FORECAST_DAYS = 90

    def __init__(self, model_dir=None):
        configured_model_dir = model_dir or os.getenv('BIODEX_AI_MODEL_DIR')
        self.model_dir = Path(configured_model_dir).expanduser() if configured_model_dir else (
            Path(__file__).resolve().parent / 'models'
        )
        self.models = {}
        self._lock = threading.RLock()

    def get_model_path(self, point_id):
        if point_id is None or isinstance(point_id, bool):
            raise ValueError('Identifiant de point de collecte invalide.')
        identifier = str(point_id).strip()
        safe_id = re.sub(r'[^A-Za-z0-9_-]', '_', identifier)
        if not identifier or len(identifier) > 64 or not safe_id:
            raise ValueError('Identifiant de point de collecte invalide.')
        digest = hashlib.sha256(identifier.encode('utf-8')).hexdigest()[:12]
        return self.model_dir / f'model_{safe_id[:40]}_{digest}.pkl'

    @staticmethod
    def _parse_date(value):
        if isinstance(value, datetime):
            return value.date()
        if isinstance(value, date):
            return value
        return date.fromisoformat(str(value)[:10])

    def _normalize_data(self, data):
        if not isinstance(data, list) or not data:
            raise ValueError('Les données doivent être une liste non vide.')
        if len(data) > self.MAX_INPUT_ROWS:
            raise ValueError(f'Le nombre de lignes ne peut pas dépasser {self.MAX_INPUT_ROWS}.')

        daily_volumes = {}
        for index, row in enumerate(data):
            if not isinstance(row, dict) or 'volume' not in row:
                raise ValueError(f'Ligne {index + 1}: le champ volume est obligatoire.')
            try:
                volume = float(row['volume'])
            except (TypeError, ValueError) as error:
                raise ValueError(f'Ligne {index + 1}: volume invalide.') from error
            if not math.isfinite(volume) or volume < 0:
                raise ValueError(f'Ligne {index + 1}: volume doit être positif ou nul.')

            if row.get('date'):
                sample_date = self._parse_date(row['date'])
            elif row.get('day') is not None:
                try:
                    day_number = int(row['day'])
                except (TypeError, ValueError) as error:
                    raise ValueError(f'Ligne {index + 1}: day invalide.') from error
                if day_number < 1:
                    raise ValueError(f'Ligne {index + 1}: day doit être supérieur ou égal à 1.')
                sample_date = date(2000, 1, 1) + timedelta(days=day_number - 1)
            else:
                raise ValueError(f'Ligne {index + 1}: date ou day est obligatoire.')

            daily_volumes[sample_date] = daily_volumes.get(sample_date, 0.0) + volume

        first_day = min(daily_volumes)
        last_day = max(daily_volumes)
        calendar_days = (last_day - first_day).days + 1
        if calendar_days > self.MAX_HISTORY_DAYS:
            raise ValueError(f'L’historique ne peut pas dépasser {self.MAX_HISTORY_DAYS} jours.')
        dates = [first_day + timedelta(days=offset) for offset in range(calendar_days)]
        volumes = [daily_volumes.get(sample_date, 0.0) for sample_date in dates]
        if len(volumes) < 2:
            raise ValueError('Il faut au moins deux jours distincts pour entraîner le modèle.')
        data_quality = {
            'recorded_days': len(daily_volumes),
            'no_record_days': calendar_days - len(daily_volumes),
            'calendar_days': calendar_days,
            'observation_coverage': round(len(daily_volumes) / calendar_days, 3),
            'no_record_day_policy': 'treated_as_zero_volume',
        }
        return dates, volumes, data_quality

    @staticmethod
    def _features(history, target_date):
        recent = np.asarray(history[-7:], dtype=float)
        previous = np.asarray(history[-14:-7], dtype=float)
        trend = float(recent.mean() - previous.mean()) if len(previous) else 0.0
        day_of_year = target_date.timetuple().tm_yday
        return [
            math.sin(2 * math.pi * target_date.weekday() / 7),
            math.cos(2 * math.pi * target_date.weekday() / 7),
            math.sin(2 * math.pi * day_of_year / 365.25),
            math.cos(2 * math.pi * day_of_year / 365.25),
            float(history[-1]),
            float(history[-7]),
            float(recent.mean()),
            float(np.mean(history[-28:])),
            trend,
        ]

    @staticmethod
    def _seasonal_baseline(dates, volumes, target_date):
        recent = np.asarray(volumes[-7:], dtype=float)
        recent_weights = np.arange(1, len(recent) + 1, dtype=float)
        recent_average = float(np.average(recent, weights=recent_weights))

        same_weekday = [
            volume for sample_date, volume in zip(dates[-28:], volumes[-28:])
            if sample_date.weekday() == target_date.weekday()
        ][-4:]
        if same_weekday:
            weekday_weights = np.arange(1, len(same_weekday) + 1, dtype=float)
            weekday_average = float(np.average(same_weekday, weights=weekday_weights))
        else:
            weekday_average = recent_average

        trend = 0.0
        if len(volumes) >= 14:
            trend = float(np.mean(volumes[-7:]) - np.mean(volumes[-14:-7])) / 7
        trend_limit = max(recent_average * 0.15, 1.0)
        trend = float(np.clip(trend, -trend_limit, trend_limit))
        return max(0.0, 0.65 * weekday_average + 0.35 * recent_average + trend)

    @staticmethod
    def _new_regressor():
        return RandomForestRegressor(
            n_estimators=160,
            max_depth=7,
            min_samples_leaf=2,
            max_features=0.9,
            random_state=42,
            n_jobs=1,
        )

    def train_model(self, point_id, data=None):
        with self._lock:
            return self._train_model(point_id, data)

    def _train_model(self, point_id, data=None):
        if data is None:
            data = point_id
            point_id = 'default'

        dates, volumes, data_quality = self._normalize_data(data)
        signature_payload = {
            'series': list(zip((value.isoformat() for value in dates), volumes)),
            'data_quality': data_quality,
        }
        signature = hashlib.sha256(
            json.dumps(signature_payload, separators=(',', ':')).encode('utf-8')
        ).hexdigest()
        key = str(point_id)
        model_path = self.get_model_path(point_id)

        bundle = self.models.get(key)
        if bundle is None and model_path.exists():
            loaded = joblib.load(model_path)
            if isinstance(loaded, dict):
                bundle = loaded
                self.models[key] = loaded
        if bundle and bundle.get('version') == self.MODEL_VERSION and bundle.get('signature') == signature:
            return self._training_details(bundle, cached=True)

        samples = []
        targets = []
        sample_indexes = []
        for index in range(7, len(volumes)):
            samples.append(self._features(volumes[:index], dates[index]))
            targets.append(volumes[index])
            sample_indexes.append(index)

        method = 'seasonal_baseline'
        validation_mae = None
        model = None
        if len(samples) >= 21:
            split_index = max(7, int(len(samples) * 0.8))
            if split_index < len(samples):
                candidate = self._new_regressor()
                candidate.fit(samples[:split_index], targets[:split_index])
                model_errors = []
                baseline_errors = []
                for offset in range(split_index, len(samples)):
                    sample_index = sample_indexes[offset]
                    predicted = max(0.0, float(candidate.predict([samples[offset]])[0]))
                    baseline = self._seasonal_baseline(
                        dates[:sample_index], volumes[:sample_index], dates[sample_index]
                    )
                    model_errors.append(abs(predicted - targets[offset]))
                    baseline_errors.append(abs(baseline - targets[offset]))

                model_mae = float(np.mean(model_errors))
                baseline_mae = float(np.mean(baseline_errors))
                validation_mae = min(model_mae, baseline_mae)
                if model_mae <= baseline_mae * 1.05:
                    model = self._new_regressor()
                    model.fit(samples, targets)
                    method = 'random_forest'
                else:
                    method = 'seasonal_baseline'

        bundle = {
            'version': self.MODEL_VERSION,
            'signature': signature,
            'dates': [sample_date.isoformat() for sample_date in dates],
            'volumes': volumes,
            'data_quality': data_quality,
            'method': method,
            'model': model,
            'validation_mae': validation_mae,
            'training_days': len(volumes),
        }
        self.model_dir.mkdir(parents=True, exist_ok=True)
        with tempfile.NamedTemporaryFile(dir=self.model_dir, suffix='.tmp', delete=False) as temporary_file:
            temporary_path = Path(temporary_file.name)
        try:
            joblib.dump(bundle, temporary_path)
            os.replace(temporary_path, model_path)
        finally:
            temporary_path.unlink(missing_ok=True)
        self.models[key] = bundle
        return self._training_details(bundle, cached=False)

    @staticmethod
    def _training_details(bundle, cached):
        return {
            'model': bundle['method'],
            'training_days': bundle['training_days'],
            'validation_mae': round(bundle['validation_mae'], 2) if bundle['validation_mae'] is not None else None,
            **bundle['data_quality'],
            'cached': cached,
        }

    def predict(self, point_id, target_date=None, day=None):
        with self._lock:
            return self._predict(point_id, target_date, day)

    def _predict(self, point_id, target_date=None, day=None):
        key = str(point_id)
        bundle = self.models.get(key)
        if bundle is None:
            model_path = self.get_model_path(point_id)
            if not model_path.exists():
                raise ValueError('Le modèle n’est pas encore entraîné pour ce point.')
            bundle = joblib.load(model_path)
            if not isinstance(bundle, dict) or bundle.get('version') != self.MODEL_VERSION:
                raise ValueError('Ancien modèle détecté: réentraînez le modèle avec des données datées.')
            self.models[key] = bundle

        dates = [date.fromisoformat(value) for value in bundle['dates']]
        volumes = list(bundle['volumes'])
        if target_date:
            forecast_date = self._parse_date(target_date)
        elif day is not None:
            try:
                day_number = int(day)
            except (TypeError, ValueError) as error:
                raise ValueError('day doit être un numéro de jour valide.') from error
            if day_number < 1 or day_number > 31:
                raise ValueError('day doit être compris entre 1 et 31.')
            forecast_date = dates[-1]
            while True:
                last_day_of_month = calendar.monthrange(forecast_date.year, forecast_date.month)[1]
                candidate = forecast_date.replace(day=min(day_number, last_day_of_month))
                if candidate >= dates[-1]:
                    forecast_date = candidate
                    break
                next_month = forecast_date.replace(day=28) + timedelta(days=4)
                forecast_date = next_month.replace(day=1)
        else:
            forecast_date = dates[-1] + timedelta(days=1)
        if forecast_date < dates[-1]:
            raise ValueError('La date de prévision ne peut pas précéder les données historiques.')
        forecast_horizon_days = (forecast_date - dates[-1]).days
        if forecast_horizon_days > self.MAX_FORECAST_DAYS:
            raise ValueError(f'L’horizon de prévision ne peut pas dépasser {self.MAX_FORECAST_DAYS} jours.')

        prediction = volumes[-1] if forecast_date == dates[-1] else None
        while dates[-1] < forecast_date:
            next_date = dates[-1] + timedelta(days=1)
            if bundle['method'] == 'random_forest' and bundle['model'] is not None:
                features = self._features(volumes, next_date)
                next_volume = max(0.0, float(bundle['model'].predict([features])[0]))
            else:
                next_volume = self._seasonal_baseline(dates, volumes, next_date)
            dates.append(next_date)
            volumes.append(next_volume)
            prediction = next_volume

        error = bundle['validation_mae']
        if error is None:
            error = float(np.std(volumes[-7:])) if len(volumes) > 1 else 0.0
        relative_error = error / max(float(prediction or 0), 1.0)
        confidence = 1 / (1 + relative_error)
        if bundle['training_days'] < 21:
            confidence *= 0.65
        confidence /= 1 + 0.08 * max(0, forecast_horizon_days - 1)

        return {
            'predicted_volume': round(max(0.0, float(prediction or 0)), 2),
            'lower_bound': round(max(0.0, float(prediction or 0) - error), 2),
            'upper_bound': round(float(prediction or 0) + error, 2),
            'confidence': round(confidence, 2),
            'confidence_type': 'relative_validation_error_not_calibrated',
            'model': bundle['method'],
            'training_days': bundle['training_days'],
            'forecast_horizon_days': forecast_horizon_days,
            **bundle['data_quality'],
        }

    def predict_volume(self, point_id, day=None):
        if day is None:
            day = point_id
            point_id = 'default'
        return self.predict(point_id, day=day)['predicted_volume']
