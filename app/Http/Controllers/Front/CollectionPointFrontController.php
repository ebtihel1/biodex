<?php

namespace App\Http\Controllers\Front;

use App\Models\CollectionPoint;
use App\Http\Controllers\Controller;

class CollectionPointFrontController extends Controller
{
    public function index()
{
    $collectionPoints = CollectionPoint::query()
        ->orderBy('name')
        ->paginate(9);

    return view('front.collectionpoints.index', compact('collectionPoints'));
}

    public function map()
    {
        return view('front.collectionpoints.map', [
            'title' => 'Carte Interactive des Points de Collecte'
        ]);
    }

    public function show($id)
    {
        $collectionPoint = CollectionPoint::where('status', 'active')->findOrFail($id);
        return view('front.collectionpoints.show', [
            'collectionPoint' => $collectionPoint,
            'title' => 'Détails du Point de Collecte'
        ]);
    }
}