<?php

namespace App\Http\Controllers\Front;

use App\Models\CollectionPoint;
use App\Http\Controllers\Controller;

class CollectionPointFrontController extends Controller
{
    public function index()
    {
        $collectionPoints = CollectionPoint::where('status', 'active')->get();
        return view('front.collectionpoints.index', [
            'collectionPoints' => $collectionPoints,
            'title' => 'Points de Collecte'
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