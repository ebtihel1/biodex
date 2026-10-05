<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\CollectionPoint;
use App\Models\Waste;

class CollectionAIController extends Controller
{
    // Tableau statique des capacités en kg par point_id
    private $capacities = [
        16 => 750,  // dar el marsa (en kg)
        20 => 1000, // le golf (en kg)
        21 => 600,  // dar el jeld (en kg)
        22 => 900,  // elmouradi (en kg)
        23 => 1250, // quatre saisons (en kg)
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

        $response = Http::timeout(30)->post('http://127.0.0.1:5000/collection/train', [
            'point_id' => $id,
            'data' => $records
        ]);

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

        $trainResponse = $this->train($id);
        if ($trainResponse->getStatusCode() === 422) {
            $trainingError = $trainResponse->getData(true);
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
            \Log::warning("Entraînement échoué pour point {$id}, utilisant fallback");
            return response()->json([
                'point_id' => $id,
                'predicted_volume' => null,
                'status' => 'unknown',
                'capacity' => $this->capacities[$id] ?? 1000, // En kg
                'last_collection_at' => $lastCollectionAt,
                'confidence' => 0,
                'forecast_model' => 'fallback',
                'error' => 'Service IA indisponible'
            ], 503);
        }

        $forecastDate = now()->addDay()->toDateString();

        $response = Http::timeout(10)->post('http://127.0.0.1:5000/collection/predict', [
            'date' => $forecastDate,
            'point_id' => $id
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $predicted_volume = max(0, (float) ($data['predicted_volume'] ?? 0));

            $capacity = $this->capacities[$id] ?? 1000; // En kg
            $ratio = $predicted_volume / $capacity;

            $status = 'normal';
            if ($ratio >= 1) $status = 'full';
            elseif ($ratio >= 0.8) $status = 'almost full';

            return response()->json([
                'point_id' => $id,
                'predicted_volume' => $predicted_volume, // En kg
                'status' => $status,
                'capacity' => $capacity, // En kg
                'ratio' => round($ratio * 100, 1) . '%',
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
            ]);
        }

        \Log::error("Échec de la prédiction pour point {$id}", $response->json());
        return response()->json([
            'point_id' => $id,
            'predicted_volume' => null,
            'status' => 'unknown',
            'capacity' => $this->capacities[$id] ?? 1000, // En kg
            'last_collection_at' => $lastCollectionAt,
            'confidence' => 0,
            'forecast_model' => 'fallback',
            'error' => 'Service IA indisponible'
        ], 503);
    }
}