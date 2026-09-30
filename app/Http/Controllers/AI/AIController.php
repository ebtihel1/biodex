<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class AIController extends Controller
{
    public function predictWaste(Request $request)
    {
        $data = [
            'type' => $request->input('type'),
            'weight' => $request->input('weight'),
            'category' => $request->input('category'),
            'description' => $request->input('description'),
        ];

        // Appel vers ton API IA Python (exécutée sur localhost:5000)
        $response = Http::post('http://127.0.0.1:5000/predict', $data);

        return response()->json([
            'prediction' => $response->json()['prediction'] ?? 'unknown'
        ]);
        return view('predictwaste', compact('prediction', 'data'));

    }
}
