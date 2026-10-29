<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display the user's cart.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login.form')
                ->with('error', 'Please login to view your cart.');
        }

        $cart = Cart::with('items.product')->firstOrCreate(['user_id' => Auth::id()]);

        // Get recommendations
        $recommendations = [];
        if (Auth::check()) {
            $service = new RecommendationService();
            $recommendations = $service->suggestForUser(Auth::user());
        }

        return view('front.cart.index', compact('cart', 'recommendations'));
    }

    /**
     * Add a product to the cart.
     */
    public function add(Request $request, Product $product)
    {
        if (!Auth::check()) {
            return redirect()->route('login.form')
                ->with('error', 'Please login to add products to your cart.');
        }

        if (!$product->is_available || $product->stock_quantity <= 0) {
            return redirect()->back()->with('error', 'This product is currently unavailable.');
        }

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + 1;
            if ($newQuantity > $product->stock_quantity) {
                return redirect()->back()->with('error', 'Not enough stock available.');
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price' => $product->price,
            ]);
        }

        return redirect()->back()->with('success', $product->name . ' has been added to your cart.');
    }

    /**
     * Update the quantity of a cart item.
     */
    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if ($cartItem->cart->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $product = $cartItem->product;
        $quantity = $request->quantity;

        if ($quantity > $product->stock_quantity) {
            return redirect()->back()->with('error', 'Not enough stock available.');
        }

        $cartItem->update(['quantity' => $quantity]);

        return redirect()->back()->with('success', 'Cart updated successfully.');
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(CartItem $cartItem)
    {
        if ($cartItem->cart->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $cartItem->delete();

        return redirect()->back()->with('success', 'Item removed from cart.');
    }

    /**
     * Clear the entire cart.
     */
    public function clear()
    {
        $cart = Cart::where('user_id', Auth::id())->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return redirect()->back()->with('success', 'Cart cleared successfully.');
    }
}
