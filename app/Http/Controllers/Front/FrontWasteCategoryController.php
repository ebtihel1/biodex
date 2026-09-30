<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WasteCategory;

class FrontWasteCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
    {
        $categories = WasteCategory::all();
        return view('front.waste-categories.list', compact('categories'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = WasteCategory::findOrFail($id);
        return view('front.waste-categories.show', compact('category'));
    }

    

    

}
