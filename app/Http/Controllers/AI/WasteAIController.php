<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WasteAIController extends Controller
{
     public function showForm()
    {
        return view('back.predictwaste'); // ou predictwaste.blade.php
    }

    // Calcule le conseil IA
    public function recycling(Request $request)
    {
        $type = $request->input('type');
        $category = $request->input('category');

        // Exemple simple d'IA basée sur catégorie
        if (strtolower($category) == 'plastic') {
            $advice = 'Recycle in the blue bin ♻️';
        } elseif (strtolower($category) == 'organic') {
            $advice = 'Can be composted 🍃';
        } else {
            $advice = 'Check local disposal rules 🗑️';
        }

        return view('back.predictwaste', compact('advice'));
    }
}
