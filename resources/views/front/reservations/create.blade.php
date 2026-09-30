{{-- resources/views/front/reservations/create.blade.php --}}
@extends('front.layout')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center bg-gradient text-dark" style="background: linear-gradient(to bottom right, #d4edda, #c3e6cb);">
    <div class="container py-3 py-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-8">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-3 p-md-4">
                        <h1 class="h3 fw-bold mb-4 text-success text-center">
                            <i class="fas fa-calendar-check me-2"></i>Make New Reservation
                        </h1>
                        
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <ul class="mb-0">
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

                        <form action="{{ route($storeRoute) }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            
                            {{-- Hidden user_id for current authenticated user --}}
                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                            <div class="mb-3">
                                <label for="product_id" class="form-label fw-semibold">
                                    <i class="fas fa-tag me-1 text-muted"></i>Product
                                </label>
                                <select name="product_id" id="product_id" class="form-select" required>
                                    <option value="">Select Product</option>
                                    @if(isset($products) && !empty($products))
                                        @foreach($products as $id => $product)
                                            <option value="{{ $id }}" {{ old('product_id') == $id ? 'selected' : '' }}>{{ $product['name'] }} ({{ $product['price'] }})</option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>No products available</option>
                                    @endif
                                </select>
                                @error('product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="quantity" class="form-label fw-semibold">
                                    <i class="fas fa-hashtag me-1 text-muted"></i>Quantity
                                </label>
                                <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" min="1" class="form-control" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="reserved_until" class="form-label fw-semibold">
                                    <i class="fas fa-calendar-alt me-1 text-muted"></i>Reserved Until
                                </label>
                                <input type="datetime-local" name="reserved_until" id="reserved_until" value="{{ old('reserved_until') }}" class="form-control" required>
                                @error('reserved_until')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                                <a href="{{ route($indexRoute) }}" class="btn btn-secondary w-100 w-md-auto">
                                    <i class="fas fa-arrow-left me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-success w-100 w-md-auto">
                                    <i class="fas fa-calendar-check me-1"></i>Reserve
                                </button>
                            </div>
                        </form>
                    </div>
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