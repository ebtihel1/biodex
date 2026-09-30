<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\WasteCategory;
use App\Models\RecyclingProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        $products = Product::with(['category', 'recyclingProcess', 'recyclingProcess.waste'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('back.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = WasteCategory::all();
        // Only completed recycling processes
        $recyclingProcesses = RecyclingProcess::where('status', 'completed')
            ->with('waste')
            ->get();
        
        return view('back.products.create', compact('categories', 'recyclingProcesses'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'waste_category_id' => 'required|exists:waste_categories,id',
            'recycling_process_id' => 'nullable|exists:recycling_processes,id',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'specifications' => 'nullable|string',
            'is_available' => 'boolean',
        ]);

        // Image upload handling
        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('products', 'public');
        }

        // Convert specifications to array if necessary
        if (isset($validated['specifications'])) {
            $validated['specifications'] = json_decode($validated['specifications'], true) 
                ?? ['description' => $validated['specifications']];
        }

        // Set is_available based on stock
        $validated['is_available'] = $validated['stock_quantity'] > 0;

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = Product::with(['category', 'recyclingProcess', 'recyclingProcess.waste', 'orders'])
            ->findOrFail($id);
        
        return view('back.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = WasteCategory::all();
        $recyclingProcesses = RecyclingProcess::where('status', 'completed')
            ->with('waste')
            ->get();
        
        return view('back.products.edit', compact('product', 'categories', 'recyclingProcesses'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'waste_category_id' => 'required|exists:waste_categories,id',
            'recycling_process_id' => 'nullable|exists:recycling_processes,id',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'specifications' => 'nullable|string',
            'is_available' => 'boolean',
        ]);

        // Image upload handling
        if ($request->hasFile('image_path')) {
            // Delete old image if it exists
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $validated['image_path'] = $request->file('image_path')->store('products', 'public');
        }

        // Convert specifications to array if necessary
        if (isset($validated['specifications'])) {
            $validated['specifications'] = json_decode($validated['specifications'], true) 
                ?? ['description' => $validated['specifications']];
        }

        // Set is_available based on stock
        $validated['is_available'] = $validated['stock_quantity'] > 0;

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Check if product has orders
        if ($product->orders()->count() > 0) {
            return redirect()->route('products.index')
                ->with('error', 'Cannot delete this product because orders are associated with it.');
        }
        
        // Delete image if it exists
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Toggle product availability
     */
    public function toggleAvailability($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['is_available' => !$product->is_available]);

        $status = $product->is_available ? 'available' : 'unavailable';
        return redirect()->route('products.index')
            ->with('success', "Product marked as {$status}.");
    }
}

