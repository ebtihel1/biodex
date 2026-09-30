<?php

namespace App\Http\Controllers\Backoffice;

use App\Models\CollectionPoint;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CollectionPointController extends Controller
{
    public function index()
    {
        $collectionPoints = CollectionPoint::all();
        return view('back.collectionpoints.index', compact('collectionPoints'));
    }

    public function create()
    {
        return view('back.collectionpoints.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'contact_phone' => 'nullable|string',
            'status' => 'required|string|in:active,inactive',
            'opening_hours' => 'nullable|json',
            'accepted_categories' => 'nullable|json',
        ]);

        CollectionPoint::create($request->all());
        return redirect()->route('collectionpoints.index')->with('success', 'Point de collecte ajouté avec succès.');
    }

    public function show($id)
    {
        $collectionPoint = CollectionPoint::findOrFail($id);
        return view('back.collectionpoints.show', compact('collectionPoint'));
    }

    public function edit($id)
    {
        $collectionPoint = CollectionPoint::findOrFail($id);
        \Log::info('CollectionPoint chargé pour édition : ', $collectionPoint->toArray());
        return view('back.collectionpoints.edit', compact('collectionPoint'));
    }

    public function update(Request $request, $id)
    {
        $collectionPoint = CollectionPoint::findOrFail($id);
        \Log::info('Données reçues pour mise à jour : ', $request->all());

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'contact_phone' => 'nullable|string',
            'status' => 'required|string|in:active,inactive',
            'opening_hours' => 'nullable|json',
            'accepted_categories' => 'nullable|json',
        ]);

        $updated = $collectionPoint->update($validatedData);
        \Log::info('Mise à jour effectuée : ', ['updated' => $updated, 'data' => $collectionPoint->toArray()]);

        if ($updated) {
            return redirect()->route('collectionpoints.index')->with('success', 'Point de collecte mis à jour avec succès.');
        } else {
            return redirect()->back()->with('error', 'Échec de la mise à jour du point de collecte.');
        }
    }

    public function destroy($id)
    {
        $collectionPoint = CollectionPoint::findOrFail($id);
        $collectionPoint->delete();
        return redirect()->route('collectionpoints.index')->with('success', 'Point de collecte supprimé.');
    }


    public function predictions()
{
    $collectionPoints = \App\Models\CollectionPoint::all();
    return view('back.collectionpoints.predictions', compact('collectionPoints'));
}
}