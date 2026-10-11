<?php

namespace App\Http\Controllers\Front;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\WasteCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Add the product to the cart, which immediately creates a pending order.
     */
    public function addToCart(Product $product)
    {
        if (!Auth::check()) {
            return redirect()->route('login.form')
                ->with('error', 'Please login to add products to your cart.');
        }

        if (!$product->is_available || $product->stock_quantity <= 0) {
            return redirect()->back()->with('error', 'This product is currently unavailable.');
        }

        Order::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'quantity' => 1,
            'total_amount' => $product->price,
            'status' => OrderStatus::Pending->value,
            'order_date' => now(),
            'shipping_address' => Auth::user()->address ?? 'To be confirmed',
            'payment_method' => 'cash',
            'tracking_number' => null,
        ]);

        $product->decrementStock(1);

        return redirect()->back()
            ->with('success', $product->name . ' has been added to your orders.');
    }
}

