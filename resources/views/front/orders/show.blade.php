{{-- resources/views/front/orders/show.blade.php --}}
@extends('front.layout')

@section('content')
@php
    $statusKey = $order->status->value ?? $order->status;
    $statusMap = [
        'pending'   => ['label' => 'Pending',   'class' => 'is-pending'],
        'confirmed' => ['label' => 'Confirmed', 'class' => 'is-confirmed'],
        'shipped'   => ['label' => 'Shipped',   'class' => 'is-shipped'],
        'delivered' => ['label' => 'Delivered', 'class' => 'is-delivered'],
        'cancelled' => ['label' => 'Cancelled', 'class' => 'is-cancelled'],
    ];
    $status = $statusMap[$statusKey] ?? ['label' => ucfirst($statusKey), 'class' => 'is-default'];
    $hasImage = $order->items->first() && $order->items->first()->product && $order->items->first()->product->image_path;
@endphp

<div class="container py-4 py-lg-5 order-show-page">

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('front.orders.index') }}">My orders</a></li>
        </ol>
    </nav>

    <section class="page-hero">
        <div class="page-hero__text">
            <span class="page-hero__eyebrow"><i class="fas fa-receipt"></i> Order details</span>
            <h1 class="page-hero__title">Order {{ $order->id }}</h1>
            <p class="page-hero__subtitle">
                Placed on {{ $order->order_date?->format('M d, Y \a\t H:i') ?? '—' }}
            </p>
        </div>
        <span class="status-pill {{ $status['class'] }} on-hero">{{ $status['label'] }}</span>
    </section>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show soft-alert" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <div class="panel">
                <div class="panel-head">
                    <h2><i class="fas fa-box-open"></i> Order Items</h2>
                </div>
                @foreach($order->items as $item)
                    <div class="product-summary">
                        <div class="product-thumb">
                            @if($item->product && $item->product->image_path)
                                <img src="{{ Storage::url($item->product->image_path) }}" alt="">
                            @else
                                <i class="fas fa-box-open"></i>
                            @endif
                        </div>
                        <div class="product-summary-text">
                            <h3>{{ $item->product->name ?? 'Product' }}</h3>
                            <p class="muted">Quantity: {{ $item->quantity }}</p>
                            <p class="price">dt{{ number_format($item->subtotal, 2) }}</p>
                        </div>
                    </div>
                @endforeach

                <dl class="detail-list">
                    <div class="is-total">
                        <dt><i class="fas fa-dollar-sign"></i> Total amount</dt>
                        <dd>dt{{ number_format($order->total_amount ?? 0, 2) }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="panel">
                <div class="panel-head">
                    <h2><i class="fas fa-truck-fast"></i> Shipping &amp; payment</h2>
                </div>
                <dl class="detail-list">
                    <div>
                        <dt><i class="fas fa-user"></i> Customer</dt>
                        <dd>{{ $order->user->name ?? 'Guest' }}</dd>
                    </div>
                    <div>
                        <dt><i class="fas fa-location-dot"></i> Shipping address</dt>
                        <dd class="wrap">{{ $order->shipping_address ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt><i class="fas fa-credit-card"></i> Payment method</dt>
                        <dd>{{ ucfirst($order->payment_method ?? '—') }}</dd>
                    </div>
                    <div>
                        <dt><i class="fas fa-route"></i> Tracking number</dt>
                        <dd>{{ $order->tracking_number ?? 'Not available' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="action-row mt-4">
                <a href="{{ route('front.orders.index') }}" class="btn btn-brand-outline">
                    <i class="fas fa-arrow-left me-1"></i>Back to orders
                </a>
                @if(($statusKey === 'pending' || $statusKey === 'confirmed'))
                    <form action="{{ route('front.orders.cancel', $order) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times me-1"></i>Cancel Order
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .order-show-page {
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
    .order-show-page .breadcrumb { --bs-breadcrumb-divider-color: #aab5af; }
    .order-show-page .breadcrumb a { color: var(--green-700); text-decoration: none; }
    .order-show-page .breadcrumb a:hover { text-decoration: underline; }

    .page-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem 2rem;
        flex-wrap: wrap;
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

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .4rem .9rem;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 700;
        background: #eef1ef;
        color: #44554c;
        white-space: nowrap;
    }
    .status-pill::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
    }
    .status-pill.is-pending   { background: #fef3c7; color: #92400e; }
    .status-pill.is-confirmed { background: #d1fae5; color: #065f46; }
    .status-pill.is-shipped   { background: #dbeafe; color: #1e40af; }
    .status-pill.is-delivered { background: #dcfce7; color: #166534; }
    .status-pill.is-cancelled { background: #fee2e2; color: #991b1b; }
    .status-pill.on-hero { background: rgba(255,255,255,.92); }

    .panel {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--radius);
        overflow: hidden;
        height: 100%;
    }
    .panel-head {
        padding: .95rem 1.25rem;
        border-bottom: 1px solid var(--line);
        background: var(--mint);
    }
    .panel-head h2 {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
        color: var(--green-700);
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .product-summary {
        display: flex;
        gap: 1rem;
        padding: 1.25rem;
        border-bottom: 1px solid var(--line);
    }
    .product-thumb {
        width: 90px;
        height: 90px;
        flex-shrink: 0;
        border-radius: 12px;
        background: var(--mint);
        color: var(--green-500);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        overflow: hidden;
    }
    .product-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .product-summary-text h3 { font-size: 1.1rem; font-weight: 700; margin: 0 0 .3rem; }
    .product-summary-text .muted { color: var(--muted); margin: 0; font-size: .88rem; }
    .product-summary-text .price { color: var(--green-700); font-size: 1.3rem; font-weight: 800; margin: .35rem 0 0; }

    .detail-list { margin: 0; }
    .detail-list > div {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        padding: .85rem 1.25rem;
        border-bottom: 1px solid var(--line);
        font-size: .9rem;
    }
    .detail-list > div:last-child { border-bottom: 0; }
    .detail-list dt { font-weight: 500; color: var(--muted); display: flex; align-items: center; gap: .5rem; }
    .detail-list dt i { color: var(--green-500); width: 14px; text-align: center; }
    .detail-list dd { margin: 0; font-weight: 600; text-align: right; }
    .detail-list dd.wrap { max-width: 60%; }
    .detail-list > div.is-total dd { color: var(--green-700); font-weight: 800; font-size: 1.1rem; }

    .action-row { display: flex; gap: .6rem; flex-wrap: wrap; }
    .action-row .btn { flex: 1 1 auto; }

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
    .soft-alert { border: 0; border-radius: 12px; background: var(--mint); color: var(--green-700); margin-bottom: 1.25rem; }

    @media (max-width: 575.98px) {
        .page-hero { padding: 1.4rem; }
        .detail-list dd.wrap { max-width: 55%; }
    }
</style>
@endsection
