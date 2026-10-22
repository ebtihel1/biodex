{{-- resources/views/front/orders/create.blade.php --}}
@extends('front.layout')

@section('content')
<div class="container py-4 py-lg-5 order-form-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('front.cart.index') }}">Cart</a></li>
            <li class="breadcrumb-item active" aria-current="page">Checkout</li>
        </ol>
    </nav>

    <section class="page-hero">
        <div class="page-hero__text">
            <span class="page-hero__eyebrow"><i class="fas fa-cart-plus"></i> Checkout</span>
            <h1 class="page-hero__title">Complete Your Order</h1>
            <p class="page-hero__subtitle">Review your items and provide delivery details.</p>
        </div>
    </section>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="panel">
                <div class="panel-body">
                    @if ($errors->any())
                        <div class="alert alert-danger soft-alert-danger" role="alert">
                            <i class="fas fa-triangle-exclamation me-2"></i>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success soft-alert" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('front.orders.store') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                        <!-- Cart Items Summary -->
                        <div class="mb-4">
                            <h5 class="mb-3"><i class="fas fa-shopping-basket me-2"></i>Order Items</h5>
                            @foreach($cart->items as $item)
                                <div class="cart-item-summary d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <div>
                                        <strong>{{ $item->product->name }}</strong>
                                        <div class="text-muted small">
                                            {{ $item->quantity }} x {{ number_format($item->unit_price, 2) }} DT
                                        </div>
                                    </div>
                                    <div class="fw-bold text-success">
                                        {{ number_format($item->subtotal, 2) }} DT
                                    </div>
                                </div>
                            @endforeach
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                <strong>Total ({{ $cart->total_quantity }} items)</strong>
                                <strong class="text-success fs-5">{{ number_format($cart->total, 2) }} DT</strong>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">
                                <i class="fas fa-location-dot me-1"></i>Shipping address
                            </label>
                            <input type="text" name="shipping_address" id="shipping_address"
                                   value="{{ old('shipping_address', auth()->user()->address ?? '') }}"
                                   class="form-control" placeholder="Street, city, postal code" required>
                            @error('shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label">
                                <i class="fas fa-money-bill me-1"></i>Payment method
                            </label>
                            <select name="payment_method" id="payment_method" class="form-select" required>
                                <option value="cash" selected>Cash on delivery</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <a href="{{ route('front.cart.index') }}" class="btn btn-brand-outline">
                                <i class="fas fa-arrow-left me-1"></i>Back to Cart
                            </a>
                            <button type="submit" class="btn btn-brand">
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

<style>
    .order-form-page {
        --green-700: #047857;
        --green-600: #059669;
        --green-500: #10b981;
        --mint:      #ecf8f2;
        --ink:       #14231c;
        --muted:     #5f7168;
        --line:      #dfe9e3;
        --radius:    16px;
        color: var(--ink);
    }
    .order-form-page .breadcrumb { --bs-breadcrumb-divider-color: #aab5af; }
    .order-form-page .breadcrumb a { color: var(--green-700); text-decoration: none; }
    .order-form-page .breadcrumb a:hover { text-decoration: underline; }

    .page-hero {
        padding: 1.6rem 2rem;
        border-radius: var(--radius);
        background:
            radial-gradient(120% 160% at 100% 0%, rgba(255,255,255,.28) 0%, rgba(255,255,255,0) 45%),
            linear-gradient(120deg, #047857 0%, #059669 55%, #10b981 100%);
        color: #fff;
        box-shadow: 0 16px 34px rgba(4, 120, 87, .22);
        margin-bottom: 1.5rem;
    }
    .page-hero__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        padding: .3rem .7rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, .18);
        margin-bottom: .7rem;
    }
    .page-hero__title { font-size: clamp(1.6rem, 3vw, 2.1rem); font-weight: 800; margin: 0 0 .3rem; }
    .page-hero__subtitle { margin: 0; opacity: .9; }

    .panel {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--radius);
        box-shadow: 0 10px 30px rgba(20, 35, 28, .06);
    }
    .panel-body { padding: 1.5rem; }

    .order-form-page .form-label {
        font-weight: 600;
        font-size: .88rem;
        color: var(--ink);
    }
    .order-form-page .form-label i { color: var(--green-500); }
    .order-form-page .form-control,
    .order-form-page .form-select {
        border-color: var(--line);
        border-radius: 10px;
        padding: .6rem .85rem;
    }
    .order-form-page .form-control:focus,
    .order-form-page .form-select:focus {
        border-color: var(--green-500);
        box-shadow: 0 0 0 .2rem rgba(16, 185, 129, .18);
    }

    .cart-item-summary {
        font-size: .95rem;
    }

    .form-actions {
        display: flex;
        flex-direction: column;
        gap: .6rem;
        margin-top: 1.5rem;
    }
    @media (min-width: 576px) {
        .form-actions { flex-direction: row; justify-content: space-between; }
    }

    .btn-brand {
        background: var(--green-700);
        border: 1px solid var(--green-700);
        color: #fff;
        font-weight: 600;
        padding: .65rem 1.25rem;
        border-radius: 10px;
        transition: background .2s ease, box-shadow .2s ease;
    }
    .btn-brand:hover, .btn-brand:focus-visible {
        background: var(--green-600);
        border-color: var(--green-600);
        color: #fff;
        box-shadow: 0 6px 16px rgba(4, 120, 87, .25);
    }
    .btn-brand-outline {
        background: #fff;
        border: 1px solid var(--green-700);
        color: var(--green-700);
        font-weight: 600;
        border-radius: 10px;
        transition: background .2s ease, color .2s ease;
    }
    .btn-brand-outline:hover, .btn-brand-outline:focus-visible {
        background: var(--green-700);
        color: #fff;
    }
    .soft-alert { border: 0; border-radius: 12px; background: var(--mint); color: var(--green-700); }
    .soft-alert-danger { border: 0; border-radius: 12px; background: #fee2e2; color: #991b1b; }

    @media (max-width: 575.98px) {
        .page-hero { padding: 1.4rem; }
        .panel-body { padding: 1.15rem; }
    }
</style>
@endsection