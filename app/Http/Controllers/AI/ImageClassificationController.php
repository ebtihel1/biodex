<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Services\WasteImageClassifier;
use Illuminate\Http\Request;

class ImageClassificationController extends Controller
{
    public function showForm()
    {
        return view('ai.classify');
    }

    public function classify(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:6144',
        ]);

        $result = app(WasteImageClassifier::class)->classify($request->file('image'));

        return response()->json([
            'success' => true,
            'category' => $result['category_code'],
            'category_label' => $result['category_label'],
            'confidence' => $result['confidence'],
            'model' => $result['model'],
            'source' => $result['source'],
            'top_categories' => $result['top_categories'] ?? [],
        ]);
    }
}
