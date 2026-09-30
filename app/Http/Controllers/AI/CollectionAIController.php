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
                DB::raw('DAY(created_at) as day'),
                DB::raw('SUM(weight) as volume') // Somme des poids en kg
            )
            ->where('collection_point_id', $id)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->groupBy('day')
            ->orderBy('day')
            ->havingRaw('SUM(weight) > 0')
            ->get()
            ->map(function ($row) {
                return [
                    'day' => (int) $row->day,
                    'volume' => (float) $row->volume ?: 0.0 // En kg
                ];
            })
            ->toArray();

        \Log::info("Données pour entraînement point {$id}", $records);

        if (empty($records)) {
            \Log::warning("Aucune donnée pour le point {$id}");
            return response()->json(['error' => 'Aucune donnée pour entraîner'], 400);
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
        return response()->json(['error' => $response->json()['error'] ?? 'Erreur serveur'], 500);
    }

    public function predict($id)
    {
        $point = CollectionPoint::findOrFail($id);

        $trainResponse = $this->train($id);
        if ($trainResponse->getStatusCode() !== 200) {
            \Log::warning("Entraînement échoué pour point {$id}, utilisant fallback");
            return response()->json([
                'point_id' => $id,
                'predicted_volume' => 100.0, // En kg
                'status' => 'normal',
                'capacity' => $this->capacities[$id] ?? 1000, // En kg
                'error' => 'Entraînement échoué, utilisant valeur par défaut'
            ]);
        }

        $next_day = min(now()->day + 1, 31);

        $response = Http::timeout(10)->post('http://127.0.0.1:5000/collection/predict', [
            'day' => $next_day,
            'point_id' => $id
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $predicted_volume = $data['predicted_volume'] ?? 0; // En kg

            $capacity = $this->capacities[$id] ?? 1000; // En kg
            $ratio = $predicted_volume / $capacity;

            $status = 'normal';
            if ($ratio > 1) $status = 'plein';
            elseif ($ratio > 0.8) $status = 'presque plein';

            return response()->json([
                'point_id' => $id,
                'predicted_volume' => $predicted_volume, // En kg
                'status' => $status,
                'capacity' => $capacity, // En kg
                'ratio' => round($ratio * 100, 1) . '%'
            ]);
        }

        \Log::error("Échec de la prédiction pour point {$id}", $response->json());
        return response()->json([
            'point_id' => $id,
            'predicted_volume' => 150.0, // En kg
            'status' => 'normal',
            'capacity' => $this->capacities[$id] ?? 1000, // En kg
            'error' => 'Service IA indisponible'
        ], 500);
    }
}