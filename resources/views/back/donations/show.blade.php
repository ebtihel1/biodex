{{-- resources/views/back/orders/create.blade.php --}}
@extends('back.layout')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card shadow">
                <div class="card-body p-3 p-md-4">
                    <h1 class="h3 fw-bold mb-4 text-success text-center">Create Order</h1>
                    @if ($errors->any())
                        <div class="alert alert-danger mb-4" role="alert">
                            <ul class="list-unstyled mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form action="{{ route('back.orders.store') }}" method="POST">
                        @csrf
                        <!-- User -->
                        <div class="mb-3">
                            <label for="user_id" class="form-label fw-bold">User</label>
                            <select name="user_id" id="user_id" class="form-select" required>
                                @foreach($users ?? \App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Product -->
                        <div class="mb-3">
                            <label for="product_id" class="form-label fw-bold">Product</label>
                            <select name="product_id" id="product_id" class="form-select" required>
                                @if(isset($products) && $products->count() > 0)
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }} ({{ $product->price }})</option>
                                    @endforeach
                                @else
                                    <option value="" disabled selected>No products available</option>
                                @endif
                            </select>
                            @error('product_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        @php
                        $rec_product = $recommendations[0] ?? null;
                        $rec_id = null;
                        if ($rec_product && isset($products)) {
                            foreach($products as $product) {
                                if ($product->name === ($rec_product['name'] ?? $rec_product->name ?? '') && $product->price == ($rec_product['price'] ?? $rec_product->price ?? 0)) {
                                    $rec_id = $product->id;
                                    break;
                                }
                            }
                        }
                        @endphp

                        @if(isset($recommendations) && count($recommendations) > 0 && $rec_id)
                            <div class="alert alert-info mb-4" role="alert">
                                <strong>AI Suggestion:</strong> Based on user history, try <strong>{{ $recommendations[0]['name'] ?? $recommendations[0]->name ?? 'Recommended Product' }}</strong> ({{ $recommendations[0]['price'] ?? $recommendations[0]->price ?? 0 }}).
                                <a href="#" class="btn btn-outline-success btn-sm ms-2" onclick="document.getElementById('product_id').value = {{ $rec_id }}; this.parentElement.style.display='none'; return false;">Select It</a>
                            </div>
                        @endif

                        <!-- Quantity -->
                        <div class="mb-3">
                            <label for="quantity" class="form-label fw-bold">Quantity</label>
                            <input type="number" min="1" name="quantity" id="quantity" value="{{ old('quantity') }}" class="form-control" required>
                            @error('quantity')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Shipping Address -->
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label fw-bold">Shipping Address</label>
                            <input type="text" name="shipping_address" id="shipping_address" value="{{ old('shipping_address') }}" class="form-control" required>
                            @error('shipping_address')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Payment Method -->
                        <div class="mb-3">
                            <label for="payment_method" class="form-label fw-bold">Payment Method</label>
                            <select name="payment_method" id="payment_method" class="form-select" required>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                                <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            </select>
                            @error('payment_method')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Tracking Number -->
                        <div class="mb-3">
                            <label for="tracking_number" class="form-label fw-bold">Tracking Number (Optional)</label>
                            <input type="text" name="tracking_number" id="tracking_number" value="{{ old('tracking_number') }}" class="form-control">
                            @error('tracking_number')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Buttons -->
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                            <a href="{{ $indexRoute }}" class="btn btn-secondary w-100 w-md-auto">Cancel</a>
                            <button type="submit" class="btn btn-success w-100 w-md-auto">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
