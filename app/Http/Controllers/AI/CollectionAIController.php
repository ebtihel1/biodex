<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\CollectionPoint;
use App\Models\Waste;

class CollectionAIController extends Controller
{
    // Tableau statique des capacités en kg par point_id
    private $capacities = [
        1 => 750,   // Centre de Collecte Tunis Nord (en kg)
        2 => 600,   // Centre de Collecte Sousse (en kg)
        3 => 900,   // Point Vert Les Berges du Lac (en kg)
        4 => 850,   // Déchetterie Charguia (en kg)
        5 => 650,   // Point de Collecte Nabeul (en kg)
        6 => 1000,  // Centre de Tri Sfax (en kg)
        // Identifiants historiques conservés pour compatibilité
        16 => 750,
        20 => 1000,
        21 => 600,
        22 => 900,
        23 => 1250,
    ];

    public function train($id)
    {
        $point = CollectionPoint::findOrFail($id);

        $records = Waste::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(weight) as volume')
            )
            ->where('collection_point_id', $id)
            ->where('created_at', '>=', now()->subDays(89)->startOfDay())
            ->where('created_at', '<=', now()->endOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($row) {
                return [
                    'date' => (string) $row->date,
                    'volume' => (float) $row->volume
                ];
            })
            ->toArray();

        \Log::info("Données pour entraînement point {$id}", $records);

        if (count($records) < 2) {
            \Log::warning("Historique insuffisant pour le point {$id}", [
                'available_days' => count($records),
                'required_days' => 2,
            ]);
            return response()->json([
                'code' => 'insufficient_training_data',
                'error' => 'Il faut au moins deux jours de collecte distincts dans les 90 derniers jours.',
                'available_days' => count($records),
                'required_days' => 2,
            ], 422);
        }

        try {
            $response = Http::timeout(30)->post('http://127.0.0.1:5000/collection/train', [
                'point_id' => $id,
                'data' => $records
            ]);
        } catch (ConnectionException $e) {
            \Log::warning("Service IA collection injoignable pour l'entraînement du point {$id}");
            return response()->json([
                'code' => 'training_failed',
                'error' => 'Le service IA est injoignable.',
            ], 503);
        }

        if ($response->successful()) {
            \Log::info("Entraînement réussi pour point {$id}", $response->json());
            return response()->json([
                'success' => true,
                'point' => $point->name,
                'records_count' => count($records),
                'flask_response' => $response->json()
            ]);
        }

        \Log::error("Échec de l'entraînement pour point {$id}", $response->json());
        return response()->json([
            'code' => 'training_failed',
            'error' => $response->json()['error'] ?? 'Le service IA a refusé les données d’entraînement.',
        ], $response->status() === 400 ? 422 : 502);
    }

    public function predict($id)
    {
        $point = CollectionPoint::findOrFail($id);
        $lastCollection = Waste::where('collection_point_id', $id)->max('created_at');
        $lastCollectionAt = $lastCollection
            ? \Carbon\Carbon::parse($lastCollection)->toIso8601String()
            : null;

        try {
            $trainResponse = $this->train($id);
        } catch (ConnectionException $e) {
            \Log::warning("Service IA injoignable (entraînement) pour le point {$id}");

            return $this->localForecast($id, $lastCollectionAt, 'AI service unreachable');
        }

        if ($trainResponse->getStatusCode() === 422) {
            $trainingError = $trainResponse->getData(true);

            if (($trainingError['code'] ?? null) !== 'insufficient_training_data') {
                return $this->localForecast($id, $lastCollectionAt, $trainingError['error'] ?? 'Training refused by the AI service.');
            }

            return response()->json([
                'point_id' => $id,
                'predicted_volume' => null,
                'status' => 'insufficient_data',
                'capacity' => $this->capacities[$id] ?? 1000,
                'last_collection_at' => $lastCollectionAt,
                'training_days' => (int) ($trainingError['available_days'] ?? 0),
                'required_training_days' => (int) ($trainingError['required_days'] ?? 2),
                'error' => $trainingError['error'] ?? 'Historique insuffisant pour produire une prévision fiable.',
            ], 422);
        }
        if ($trainResponse->getStatusCode() !== 200) {
            \Log::warning("Entraînement échoué pour point {$id}, utilisant la prévision locale");

            return $this->localForecast($id, $lastCollectionAt, 'Model training unavailable.');
        }

        $forecastDate = now()->addDay()->toDateString();

        try {
            $response = Http::timeout(10)->post('http://127.0.0.1:5000/collection/predict', [
                'date' => $forecastDate,
                'point_id' => $id
            ]);
        } catch (ConnectionException $e) {
            \Log::warning("Service IA injoignable (prévision) pour le point {$id}");

            return $this->localForecast($id, $lastCollectionAt, 'AI service unreachable');
        }

        if ($response->successful()) {
            $data = $response->json();
            $predicted_volume = max(0, (float) ($data['predicted_volume'] ?? 0));

            $capacity = $this->capacities[$id] ?? 1000; // En kg
            $ratio = $predicted_volume / $capacity;

            return response()->json([
                'point_id' => $id,
                'predicted_volume' => $predicted_volume, // En kg
                'status' => $this->statusFor($ratio),
                'capacity' => $capacity, // En kg
                'ratio' => round($ratio * 100, 1),
                'forecast_date' => $forecastDate,
                'last_collection_at' => $lastCollectionAt,
                'confidence' => (float) ($data['confidence'] ?? 0),
                'lower_bound' => (float) ($data['lower_bound'] ?? $predicted_volume),
                'upper_bound' => (float) ($data['upper_bound'] ?? $predicted_volume),
                'forecast_model' => $data['model'] ?? 'unknown',
                'training_days' => (int) ($data['training_days'] ?? 0),
                'confidence_type' => $data['confidence_type'] ?? 'unknown',
                'recorded_days' => (int) ($data['recorded_days'] ?? 0),
                'no_record_days' => (int) ($data['no_record_days'] ?? 0),
                'calendar_days' => (int) ($data['calendar_days'] ?? 0),
                'observation_coverage' => (float) ($data['observation_coverage'] ?? 0),
                'no_record_day_policy' => $data['no_record_day_policy'] ?? 'unknown',
                'forecast_source' => 'ai_service',
            ]);
        }

        \Log::error("Échec de la prédiction pour point {$id}", $response->json());

        return $this->localForecast($id, $lastCollectionAt, 'The AI service returned an error.');
    }

    /**
     * Prévisions compactes de tous les points de collecte (pour la carte interactive).
     */
    public function forecasts()
    {
        $points = CollectionPoint::query()->orderBy('id')->get();

        $payload = $points->map(function (CollectionPoint $point) {
            $volumes = $this->dailyVolumes((int) $point->id);
            $capacity = $this->capacities[$point->id] ?? 1000;

            $base = [
                'id' => (int) $point->id,
                'name' => $point->name,
                'address' => $point->address,
                'city' => $point->city,
                'latitude' => (float) $point->latitude,
                'longitude' => (float) $point->longitude,
                'status' => $point->status,
                'capacity_kg' => $capacity,
                'accepted_categories' => $point->accepted_categories ?? [],
            ];

            if (count($volumes) < 2) {
                return $base + [
                    'predicted_volume_kg' => null,
                    'ratio_pct' => null,
                    'level' => 'insufficient',
                    'forecast_date' => null,
                    'confidence' => null,
                    'training_days' => count($volumes),
                ];
            }

            $values = array_values($volumes);
            $recent = array_slice($values, -14);
            $prediction = array_sum($recent) / count($recent);
            $ratio = $prediction / $capacity;
            $coverage = min(1.0, count($values) / 90);

            return $base + [
                'predicted_volume_kg' => round(max(0, $prediction), 2),
                'ratio_pct' => round($ratio * 100, 1),
                'level' => $this->levelFor($ratio),
                'forecast_date' => now()->addDay()->toDateString(),
                'confidence' => round(min(0.6, 0.25 + 0.35 * $coverage), 2),
                'training_days' => count($values),
            ];
        });

        return response()->json([
            'generated_at' => now()->toIso8601String(),
            'points' => $payload,
        ]);
    }

    /**
     * Statut de capacité partagé par toutes les réponses de prévision.
     */
    private function statusFor(float $ratio): string
    {
        if ($ratio >= 1) {
            return 'full';
        }
        if ($ratio >= 0.8) {
            return 'almost_full';
        }

        return 'normal';
    }

    /**
     * Prévision de repli calculée à partir de l'historique de la base,
     * utilisée quand le service IA (port 5000) est indisponible.
     */
    private function localForecast(int $id, ?string $lastCollectionAt, string $reason)
    {
        $volumes = $this->dailyVolumes($id);
        $capacity = $this->capacities[$id] ?? 1000;

        if (count($volumes) < 2) {
            return response()->json([
                'point_id' => $id,
                'predicted_volume' => null,
                'status' => 'insufficient_data',
                'capacity' => $capacity,
                'last_collection_at' => $lastCollectionAt,
                'training_days' => count($volumes),
                'required_training_days' => 2,
                'error' => 'Il faut au moins deux jours de collecte distincts dans les 90 derniers jours.',
            ], 422);
        }

        $values = array_values($volumes);
        $recent = array_slice($values, -14);
        $prediction = array_sum($recent) / count($recent);
        $variance = array_sum(array_map(fn ($volume) => ($volume - $prediction) ** 2, $recent)) / count($recent);
        $spread = sqrt($variance);

        $ratio = $prediction / $capacity;
        $coverage = min(1.0, count($values) / 90);

        return response()->json([
            'point_id' => $id,
            'predicted_volume' => round(max(0, $prediction), 2),
            'status' => $this->statusFor($ratio),
            'capacity' => $capacity,
            'ratio' => round($ratio * 100, 1),
            'forecast_date' => now()->addDay()->toDateString(),
            'last_collection_at' => $lastCollectionAt,
            'confidence' => round(min(0.6, 0.25 + 0.35 * $coverage), 2),
            'lower_bound' => round(max(0, $prediction - $spread), 2),
            'upper_bound' => round($prediction + $spread, 2),
            'forecast_model' => 'local_recent_average',
            'training_days' => count($values),
            'confidence_type' => 'heuristic_fallback_not_calibrated',
            'recorded_days' => count($values),
            'no_record_days' => max(0, 90 - count($values)),
            'calendar_days' => 90,
            'observation_coverage' => round($coverage, 3),
            'no_record_day_policy' => 'excluded_from_local_average',
            'forecast_source' => 'local_database',
            'fallback_reason' => $reason,
        ]);
    }

    /**
     * Niveau de remplissage prédit : low / moderate / high.
     */
    private function levelFor(float $ratio): string
    {
        if ($ratio >= 0.8) {
            return 'high';
        }
        if ($ratio >= 0.5) {
            return 'moderate';
        }

        return 'low';
    }

    /**
     * Volume journalier (kg) par date sur les 90 derniers jours.
     */
    private function dailyVolumes(int $pointId): array
    {
        $records = Waste::where('collection_point_id', $pointId)
            ->where('created_at', '>=', now()->subDays(89)->startOfDay())
            ->get(['created_at', 'weight']);

        $volumes = [];
        foreach ($records as $record) {
            $date = $record->created_at->toDateString();
            $volumes[$date] = ($volumes[$date] ?? 0) + (float) $record->weight;
        }
        ksort($volumes);

        return $volumes;
    }
}