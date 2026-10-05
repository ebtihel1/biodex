import unittest
from datetime import date, timedelta
from tempfile import TemporaryDirectory

from collection_ai_service import CollectionAIService


class CollectionAIServiceTests(unittest.TestCase):
    def test_forecast_learns_weekly_pattern_and_reuses_model(self):
        start = date(2026, 1, 1)
        history = []
        for offset in range(90):
            sample_date = start + timedelta(days=offset)
            volume = 80 if sample_date.weekday() == 0 else 12
            history.append({'date': sample_date.isoformat(), 'volume': volume})

        with TemporaryDirectory() as model_dir:
            service = CollectionAIService(model_dir)
            training = service.train_model('weekly', history)
            last_day = start + timedelta(days=89)
            next_monday = last_day + timedelta(days=(7 - last_day.weekday()) % 7 or 7)
            monday = service.predict('weekly', target_date=next_monday)
            tuesday = service.predict('weekly', target_date=next_monday + timedelta(days=1))
            cached = service.train_model('weekly', history)

        self.assertEqual(training['training_days'], 90)
        self.assertIsNotNone(training['validation_mae'])
        self.assertGreater(monday['predicted_volume'], tuesday['predicted_volume'] * 1.5)
        self.assertGreaterEqual(monday['lower_bound'], 0)
        self.assertTrue(cached['cached'])

    def test_training_rejects_negative_volumes(self):
        with TemporaryDirectory() as model_dir:
            service = CollectionAIService(model_dir)
            with self.assertRaises(ValueError):
                service.train_model('invalid', [
                    {'date': '2026-01-01', 'volume': -1},
                    {'date': '2026-01-02', 'volume': 2},
                ])

    def test_model_reloads_and_legacy_day_rolls_into_next_month(self):
        start = date(2026, 3, 1)
        history = [
            {'date': (start + timedelta(days=offset)).isoformat(), 'volume': 10 + offset}
            for offset in range(31)
        ]

        with TemporaryDirectory() as model_dir:
            trainer = CollectionAIService(model_dir)
            trainer.train_model(22, history)
            expected = trainer.predict(22, day=1)
            reloaded = CollectionAIService(model_dir)
            forecast = reloaded.predict(22, day=1)

        self.assertEqual(forecast['forecast_horizon_days'], 1)
        self.assertEqual(forecast, expected)

    def test_training_reports_days_without_records(self):
        with TemporaryDirectory() as model_dir:
            service = CollectionAIService(model_dir)
            result = service.train_model('gaps', [
                {'date': '2026-01-01', 'volume': 5},
                {'date': '2026-01-03', 'volume': 7},
            ])

        self.assertEqual(result['recorded_days'], 2)
        self.assertEqual(result['no_record_days'], 1)
        self.assertEqual(result['observation_coverage'], 0.667)
        self.assertEqual(result['no_record_day_policy'], 'treated_as_zero_volume')

    def test_model_paths_do_not_collide_and_forecast_horizon_is_bounded(self):
        start = date(2026, 1, 1)
        history = [
            {'date': (start + timedelta(days=offset)).isoformat(), 'volume': 10}
            for offset in range(10)
        ]
        with TemporaryDirectory() as model_dir:
            service = CollectionAIService(model_dir)
            service.train_model('point/1', history)
            self.assertNotEqual(
                service.get_model_path('point/1'),
                service.get_model_path('point?1'),
            )
            with self.assertRaises(ValueError):
                service.predict('point/1', target_date=start + timedelta(days=101))


if __name__ == '__main__':
    unittest.main()
