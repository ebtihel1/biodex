<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\WasteCategory;
use Illuminate\Http\Request;

class ProductFrontController extends Controller
{
    /**
     * Display a listing of available products for customers.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'recyclingProcess'])
            ->where('is_available', true)
            ->where('stock_quantity', '>', 0);

        // Filter by category
        if ($request->has('category') && $request->category != '') {
            $query->where('waste_category_id', $request->category);
        }

        // Search by name
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default: // latest
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(12);
        $categories = WasteCategory::all();

        return view('front.products.index', compact('products', 'categories'));
    }

    /**
     * Display the specified product details.
     */
    public function show($id)
    {
        $product = Product::with(['category', 'recyclingProcess', 'recyclingProcess.waste'])
            ->where('is_available', true)
            ->findOrFail($id);

        // Similar products (same category)
        $similarProducts = Product::where('waste_category_id', $product->waste_category_id)
            ->where('id', '!=', $product->id)
            ->where('is_available', true)
            ->where('stock_quantity', '>', 0)
            ->limit(4)
            ->get();

        return view('front.products.show', compact('product', 'similarProducts'));
    }
}

