<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecyclingAIController extends Controller
{
    private $aiServiceUrl = 'http://localhost:5002';

    /**
     * Classifier un déchet avec l'IA
     */
    public function classifyWaste(Request $request)
    {
        try {
            $response = Http::timeout(10)->post($this->aiServiceUrl . '/classify-waste', [
                'type' => $request->input('type'),
                'weight' => $request->input('weight'),
                'description' => $request->input('description')
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'error' => 'Service IA indisponible'
            ], 503);

        } catch (\Exception $e) {
            Log::error('Erreur classification IA: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la classification'
            ], 500);
        }
    }

    /**
     * Prédire la qualité d'un produit recyclé
     */
    public function predictQuality(Request $request)
    {
        try {
            $response = Http::timeout(10)->post($this->aiServiceUrl . '/predict-quality', [
                'waste_type' => $request->input('waste_type'),
                'recycling_method' => $request->input('recycling_method'),
                'waste_condition' => $request->input('waste_condition', 'good'),
                'storage_days' => $request->input('storage_days', 0)
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'error' => 'Service IA indisponible'
            ], 503);

        } catch (\Exception $e) {
            Log::error('Erreur prédiction qualité IA: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la prédiction de qualité'
            ], 500);
        }
    }

    /**
     * Estimer le prix d'un produit recyclé
     */
    public function estimatePrice(Request $request)
    {
        try {
            $response = Http::timeout(10)->post($this->aiServiceUrl . '/estimate-price', [
                'product_name' => $request->input('product_name'),
                'waste_category' => $request->input('waste_category'),
                'quality_score' => $request->input('quality_score', 70),
                'recycling_cost' => $request->input('recycling_cost', 10),
                'market_demand' => $request->input('market_demand', 50)
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'error' => 'Service IA indisponible'
            ], 503);

        } catch (\Exception $e) {
            Log::error('Erreur estimation prix IA: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de l\'estimation du prix'
            ], 500);
        }
    }

    /**
     * Générer une description de produit
     */
    public function generateDescription(Request $request)
    {
        try {
            $response = Http::timeout(10)->post($this->aiServiceUrl . '/generate-description', [
                'product_name' => $request->input('product_name'),
                'source_material' => $request->input('source_material'),
                'recycling_method' => $request->input('recycling_method'),
                'specifications' => $request->input('specifications', [])
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'error' => 'Service IA indisponible'
            ], 503);

        } catch (\Exception $e) {
            Log::error('Erreur génération description IA: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la génération de description'
            ], 500);
        }
    }

    /**
     * Optimiser un processus de recyclage
     */
    public function optimizeProcess(Request $request)
    {
        try {
            $response = Http::timeout(10)->post($this->aiServiceUrl . '/optimize-process', [
                'process_data' => $request->input('process_data', []),
                'current_efficiency' => $request->input('current_efficiency', 70)
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'error' => 'Service IA indisponible'
            ], 503);

        } catch (\Exception $e) {
            Log::error('Erreur optimisation processus IA: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de l\'optimisation du processus'
            ], 500);
        }
    }

    /**
     * Vérifier l'état du service IA
     */
    public function healthCheck()
    {
        try {
            $response = Http::timeout(5)->get($this->aiServiceUrl . '/health');

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'status' => 'unhealthy',
                'error' => 'Service IA indisponible'
            ], 503);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'unhealthy',
                'error' => 'Impossible de contacter le service IA'
            ], 503);
        }
    }
}
