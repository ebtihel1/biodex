{{-- resources/views/front/orders/create.blade.php --}}
@extends('front.layout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card shadow">
                <div class="card-body p-3 p-md-4">
                    <h1 class="h3 font-weight-bold mb-4 text-success text-center">
                        <i class="fas fa-shopping-cart me-2"></i>Place New Order
                    </h1>
                    
                    @if ($errors->any())
                        <div class="alert alert-danger mb-4" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <ul class="list-unstyled mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success mb-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ $storeRoute }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        
                        {{-- Hidden user_id for current authenticated user --}}
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                        <div class="mb-3">
                            <label for="product_id" class="form-label fw-bold">
                                <i class="fas fa-tag me-1 text-muted"></i>Product
                            </label>
                            <select name="product_id" id="product_id" class="form-select" required>
                                <option value="">Select Product</option>
                                @if(isset($products) && $products->count() > 0)
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }} ({{ $product->price }})</option>
                                    @endforeach
                                @else
                                    <option value="" disabled>No products available</option>
                                @endif
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
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

                        <div class="mb-3">
                            <label for="quantity" class="form-label fw-bold">
                                <i class="fas fa-hashtag me-1 text-muted"></i>Quantity
                            </label>
                            <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" min="1" class="form-control" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="shipping_address" class="form-label fw-bold">
                                <i class="fas fa-map-marker-alt me-1 text-muted"></i>Shipping Address
                            </label>
                            <input type="text" name="shipping_address" id="shipping_address" value="{{ old('shipping_address') }}" class="form-control" required>
                            @error('shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label fw-bold">
                                <i class="fas fa-credit-card me-1 text-muted"></i>Payment Method
                            </label>
                            <select name="payment_method" id="payment_method" class="form-select" required>
                                <option value="">Select Method</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                                <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tracking_number" class="form-label fw-bold">
                                <i class="fas fa-route me-1 text-muted"></i>Tracking Number (Optional)
                            </label>
                            <input type="text" name="tracking_number" id="tracking_number" value="{{ old('tracking_number') }}" class="form-control">
                            @error('tracking_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                            <a href="{{ $indexRoute }}" class="btn btn-secondary w-100 w-md-auto">
                                <i class="fas fa-arrow-left me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success w-100 w-md-auto">
                                <i class="fas fa-paper-plane me-1"></i>Place Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Bootstrap validation
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
@endsection
