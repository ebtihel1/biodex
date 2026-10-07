<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Waste;
use App\Services\WasteImageClassifier;
use Illuminate\Http\Request;

class FrontWasteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $wastes = Waste::all();

        return view('front.wastes.index', compact('wastes'));

    }

    /**
     * Show the form for creating a new waste.
     */
    public function create()
    {
        return view('front.wastes.create');
    }

    /**
     * Store a newly created waste in storage.
     */
    public function store(Request $request)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $data = $request->validate([
            'type' => 'nullable|string|max:255',
            'weight' => 'required|numeric|min:0',
            'status' => 'nullable|string|max:50',
            'waste_category_id' => 'required|exists:waste_categories,id',
            'collection_point_id' => 'required|exists:collection_points,id',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:6144',
        ]);

        $data['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $data['image_path'] = $image->store('wastes', 'public');

            // Classification automatique par CNN (service Python + repli PHP)
            $classifier = app(WasteImageClassifier::class);
            $classification = $classifier->classify($image);
            if ($classification['category_code'] !== 'unknown') {
                $data['ai_classification'] = $classification['category_label'];
                $data['ai_confidence'] = $classification['confidence'];

                $aiCategoryId = $classifier->categoryIdForCode($classification['category_code']);
                // La photo prédit la catégorie : on privilégie l'IA si elle est sûre.
                if ($aiCategoryId && $classification['confidence'] >= 0.5) {
                    $data['waste_category_id'] = $aiCategoryId;
                }
                if (empty($data['type'])) {
                    $data['type'] = $classification['category_label'];
                }
            }
        }

        $data['type'] = trim($data['type'] ?? ($data['ai_classification'] ?? '')) ?: 'Non spécifié';
        $data['description'] = trim($data['description'] ?? '') ?: '—';
        $data['status'] = $data['status'] ?? 'recyclable';

        $waste = Waste::create($data);

        return redirect()->route('front.wastes.show', $waste->id)
            ->with('success', 'Waste added successfully'.($data['ai_classification'] ?? '' ? ' — category detected by AI' : ''));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $wastes = Waste::findOrFail($id);

        return view('front.wastes.show', compact('wastes'));
    }
}
