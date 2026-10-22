@extends('front.layout')

@section('content')
<div class="container py-4 py-lg-5 cart-page">
    <!-- Flash messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <section class="page-hero">
        <div class="page-hero__text">
            <span class="page-hero__eyebrow"><i class="fas fa-shopping-cart"></i> Shopping Cart</span>
            <h1 class="page-hero__title">Your Cart</h1>
            <p class="page-hero__subtitle">Review your items before checkout.</p>
        </div>
    </section>

    @if($cart->items->isEmpty())
        <div class="empty-state">
            <i class="fas fa-shopping-basket"></i>
            <h2>Your cart is empty</h2>
            <p>Browse our shop and add some products to get started.</p>
            <a href="{{ route('front.products.index') }}" class="btn btn-brand">
                <i class="fas fa-store me-2"></i>Browse Products
            </a>
        </div>
    @else
        <div class="row g-4">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card cart-items-card">
                    <div class="card-body">
                        @foreach($cart->items as $item)
                            <div class="cart-item">
                                <div class="row align-items-center">
                                    <div class="col-3 col-sm-2">
                                        @if($item->product->image_path)
                                            <img src="{{ asset('storage/' . $item->product->image_path) }}"
                                                 alt="{{ $item->product->name }}"
                                                 class="cart-item-image">
                                        @else
                                            <div class="cart-item-placeholder">
                                                <i class="fas fa-box"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-9 col-sm-4">
                                        <h5 class="cart-item-name">{{ $item->product->name }}</h5>
                                        <p class="cart-item-price text-success fw-bold">
                                            {{ number_format($item->unit_price, 2) }} DT
                                        </p>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <form action="{{ route('front.cart.update', $item) }}" method="POST" class="quantity-form">
                                            @csrf
                                            @method('PUT')
                                            <div class="input-group input-group-sm">
                                                <input type="number" name="quantity" value="{{ $item->quantity }}"
                                                       min="1" max="{{ $item->product->stock_quantity }}"
                                                       class="form-control" required>
                                                <button type="submit" class="btn btn-outline-success">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-3 col-sm-1 text-end">
                                        <p class="cart-item-subtotal fw-bold">
                                            {{ number_format($item->subtotal, 2) }} DT
                                        </p>
                                    </div>
                                    <div class="col-12 col-sm-2 text-end mt-2 mt-sm-0">
                                        <form action="{{ route('front.cart.remove', $item) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="card cart-summary-card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Order Summary</h4>

                        <div class="summary-row">
                            <span>Subtotal ({{ $cart->total_quantity }} items)</span>
                            <span class="fw-bold">{{ number_format($cart->total, 2) }} DT</span>
                        </div>

                        <div class="summary-row summary-total">
                            <span>Total</span>
                            <span class="fw-bold text-success">{{ number_format($cart->total, 2) }} DT</span>
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <a href="{{ route('front.orders.create') }}" class="btn btn-brand btn-lg">
                                <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                            </a>

                            <form action="{{ route('front.cart.clear') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-trash-alt me-2"></i>Clear Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .cart-page {
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

    .page-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem 2rem;
        flex-wrap: wrap;
        padding: 1.75rem 2rem;
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

    .page-hero__title {
        font-size: clamp(1.7rem, 3vw, 2.3rem);
        font-weight: 800;
        letter-spacing: -.02em;
        margin: 0 0 .3rem;
    }

    .page-hero__subtitle { margin: 0; opacity: .9; }

    .cart-items-card, .cart-summary-card {
        border: 1px solid var(--line);
        border-radius: var(--radius);
        box-shadow: 0 10px 30px rgba(20, 35, 28, .06);
    }

    .cart-item {
        padding: 1.25rem;
        border-bottom: 1px solid var(--line);
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .cart-item-image {
        width: 100%;
        height: 80px;
        object-fit: cover;
        border-radius: 10px;
    }

    .cart-item-placeholder {
        width: 100%;
        height: 80px;
        background: var(--mint);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--green-500);
        font-size: 1.5rem;
    }

    .cart-item-name {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: .25rem;
        color: var(--ink);
    }

    .cart-item-price {
        font-size: .9rem;
        margin: 0;
    }

    .cart-item-subtotal {
        font-size: 1.1rem;
        margin: 0;
        color: var(--green-700);
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: .75rem 0;
        font-size: .95rem;
        color: var(--muted);
    }

    .summary-total {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--ink);
        border-top: 2px solid var(--line);
        margin-top: .5rem;
        padding-top: 1rem;
    }

    .btn-brand {
        background: var(--green-700);
        border-color: var(--green-700);
        color: #fff;
        font-weight: 600;
        border-radius: 10px;
        transition: background .2s ease, box-shadow .2s ease;
    }

    .btn-brand:hover {
        background: var(--green-600);
        border-color: var(--green-600);
        color: #fff;
        box-shadow: 0 6px 16px rgba(4, 120, 87, .25);
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: #fff;
        border: 1px dashed var(--line);
        border-radius: var(--radius);
    }

    .empty-state > i {
        font-size: 4rem;
        color: var(--green-500);
        margin-bottom: 1.5rem;
    }

    .empty-state h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: .5rem;
        color: var(--ink);
    }

    .empty-state p {
        color: var(--muted);
        margin-bottom: 1.5rem;
    }
</style>
@endsection
