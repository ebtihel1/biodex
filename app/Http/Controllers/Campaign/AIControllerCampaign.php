<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIControllerCampaign extends Controller
{
    private string $aiServiceUrl = 'http://127.0.0.1:5003';

    public function askAI(Request $request)
    {
        $validated = $request->validate([
            'prompt' => 'nullable|string|max:1000',
        ]);

        $prompt = $validated['prompt'] ?? "Bonjour, écris-moi un haïku sur l'automne.";

        try {
            $response = Http::timeout(60)->get($this->aiServiceUrl . '/ask', [
                'prompt' => $prompt,
            ]);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Campaign AI service returned ' . $response->status(),
                ], 502);
            }

            return response()->json($response->json());
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Campaign AI service unreachable on ' . $this->aiServiceUrl,
            ], 503);
        }
    }
}
