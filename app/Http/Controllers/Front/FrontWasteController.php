<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Waste;

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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $wastes = Waste::findOrFail($id);
        return view('front.wastes.show', compact('wastes'));
    }

    
}
