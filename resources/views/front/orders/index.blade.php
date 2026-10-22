{{-- resources/views/front/orders/index.blade.php --}}
@extends('front.layout')

@section('content')
@php
    $orders->withQueryString();

    $statusMap = [
        'pending'   => ['label' => 'Pending',   'class' => 'is-pending'],
        'confirmed' => ['label' => 'Confirmed', 'class' => 'is-confirmed'],
        'shipped'   => ['label' => 'Shipped',   'class' => 'is-shipped'],
        'delivered' => ['label' => 'Delivered', 'class' => 'is-delivered'],
        'cancelled' => ['label' => 'Cancelled', 'class' => 'is-cancelled'],
    ];

    $totals = [
        'total'     => (int) ($summary->total_count ?? $orders->total()),
        'pending'   => (int) ($summary->pending_count ?? 0),
        'shipped'   => (int) ($summary->shipped_count ?? 0),
        'delivered' => (int) ($summary->delivered_count ?? 0),
    ];

    $hasFilters = request()->filled('date_from') || request()->filled('date_to') || request()->filled('product_search');
@endphp

<div class="container py-4 py-lg-5 orders-page">

    <section class="page-hero">
        <div class="page-hero__text">
            <span class="page-hero__eyebrow"><i class="fas fa-bag-shopping"></i> My purchases</span>
            <h1 class="page-hero__title">My orders</h1>
            <p class="page-hero__subtitle">Track and manage your waste transformation orders.</p>
        </div>
        <a href="{{ route($createRoute ?? 'front.orders.create') }}" class="btn btn-brand">
            <i class="fas fa-plus me-2"></i>New order
        </a>
    </section>

    <section class="stats-row" aria-label="Order summary">
        <div class="stat-card">
            <span class="stat-icon"><i class="fas fa-boxes-stacked"></i></span>
            <div>
                <span class="stat-value">{{ number_format($totals['total']) }}</span>
                <span class="stat-label">Total orders</span>
            </div>
        </div>
        <div class="stat-card is-pending">
            <span class="stat-icon"><i class="fas fa-hourglass-half"></i></span>
            <div>
                <span class="stat-value">{{ number_format($totals['pending']) }}</span>
                <span class="stat-label">Pending</span>
            </div>
        </div>
        <div class="stat-card is-shipped">
            <span class="stat-icon"><i class="fas fa-truck-fast"></i></span>
            <div>
                <span class="stat-value">{{ number_format($totals['shipped']) }}</span>
                <span class="stat-label">Shipped</span>
            </div>
        </div>
        <div class="stat-card is-delivered">
            <span class="stat-icon"><i class="fas fa-circle-check"></i></span>
            <div>
                <span class="stat-value">{{ number_format($totals['delivered']) }}</span>
                <span class="stat-label">Delivered</span>
            </div>
        </div>
    </section>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show soft-alert" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="GET" action="{{ route('front.orders.index') }}" class="filter-bar" role="search">
        <div class="filter-field">
            <label for="product_search" class="form-label">Product</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" name="product_search" id="product_search" class="form-control"
                       value="{{ request('product_search') }}" placeholder="Search by product name">
            </div>
        </div>
        <div class="filter-field">
            <label for="date_from" class="form-label">From</label>
            <input type="date" name="date_from" id="date_from" class="form-control"
                   value="{{ request('date_from') }}" max="{{ now()->format('Y-m-d') }}">
        </div>
        <div class="filter-field">
            <label for="date_to" class="form-label">To</label>
            <input type="date" name="date_to" id="date_to" class="form-control"
                   value="{{ request('date_to') }}" max="{{ now()->format('Y-m-d') }}">
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn-brand">
                <i class="fas fa-filter me-1"></i>Filter
            </button>
            @if($hasFilters)
                <a href="{{ route('front.orders.index') }}" class="btn btn-link link-clear">Clear</a>
            @endif
        </div>
    </form>

    @unless($orders->isEmpty())
        <div class="result-meta">
            <span><strong>{{ $orders->total() }}</strong> {{ Str::plural('order', $orders->total()) }}</span>
            <span>Showing {{ $orders->firstItem() }}–{{ $orders->lastItem() }}</span>
        </div>
    @endunless

    <div class="row g-3 g-lg-4">
        @forelse($orders as $order)
            @php
                $statusKey = $order->status->value ?? $order->status;
                $status    = $statusMap[$statusKey] ?? ['label' => ucfirst($statusKey), 'class' => 'is-default'];
                $firstItem = $order->items->first();
                $hasImage  = $firstItem && $firstItem->product && $firstItem->product->image_path;
            @endphp

            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <article class="order-card">
                    <a href="{{ route('front.orders.show', $order) }}" class="order-media" tabindex="-1" aria-hidden="true">
                        @if($hasImage)
                            <img src="{{ Storage::url($firstItem->product->image_path) }}" alt="" loading="lazy">
                        @else
                            <i class="fas fa-box-open"></i>
                        @endif
                        <span class="status-pill {{ $status['class'] }}">{{ $status['label'] }}</span>
                    </a>

                    <div class="order-body">
                        <div class="order-top">
                            <time class="order-date" datetime="{{ $order->order_date?->toDateString() }}">
                                <i class="far fa-calendar"></i>{{ $order->order_date?->format('M d, Y') ?? '—' }}
                            </time>
                        </div>

                        <h2 class="order-title" title="{{ $firstItem->product->name ?? '' }}">
                            {{ $order->items->count() > 1
                                ? $order->items->count() . ' items'
                                : Str::limit($firstItem->product->name ?? 'Product', 40) }}
                        </h2>

                        <dl class="order-meta">
                            <div>
                                <dt>Items</dt>
                                <dd>{{ $order->total_quantity }}</dd>
                            </div>
                            <div>
                                <dt>Total</dt>
                                <dd class="is-price">{{ number_format($order->total_amount ?? 0, 2) }} DT</dd>
                            </div>
                        </dl>

                        <a href="{{ route('front.orders.show', $order) }}" class="btn btn-brand-outline btn-sm w-100 mt-auto">
                            View details <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-shopping-cart"></i>
                    @if($hasFilters)
                        <h2>No orders match your filters</h2>
                        <p>Try a wider date range or a different search term.</p>
                        <a href="{{ route('front.orders.index') }}" class="btn btn-brand-outline">Clear filters</a>
                    @else
                        <h2>No orders yet</h2>
                        <p>Browse the shop and place your first order.</p>
                        <a href="{{ route($createRoute ?? 'front.orders.create') }}" class="btn btn-brand">
                            <i class="fas fa-plus me-2"></i>Place your first order
                        </a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    @if($orders->hasPages())
        <nav class="mt-5" aria-label="Orders pagination">
            <ul class="pagination pagination-modern justify-content-center">
                <li class="page-item {{ $orders->onFirstPage() ? 'disabled' : '' }}">
                    @if($orders->onFirstPage())
                        <span class="page-link"><i class="fas fa-chevron-left me-1"></i>Previous</span>
                    @else
                        <a class="page-link" href="{{ $orders->previousPageUrl() }}" rel="prev">
                            <i class="fas fa-chevron-left me-1"></i>Previous
                        </a>
                    @endif
                </li>

                @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                    <li class="page-item {{ $page == $orders->currentPage() ? 'active' : '' }}">
                        @if($page == $orders->currentPage())
                            <span class="page-link" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    </li>
                @endforeach

                <li class="page-item {{ $orders->hasMorePages() ? '' : 'disabled' }}">
                    @if($orders->hasMorePages())
                        <a class="page-link" href="{{ $orders->nextPageUrl() }}" rel="next">
                            Next<i class="fas fa-chevron-right ms-1"></i>
                        </a>
                    @else
                        <span class="page-link">Next<i class="fas fa-chevron-right ms-1"></i></span>
                    @endif
                </li>
            </ul>
        </nav>
    @endif
</div>

<style>
    .orders-page {
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

    /* ===== Bannière ===== */
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
        margin-bottom: 1.25rem;
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
    .page-hero .btn-brand {
        background: #fff;
        border-color: #fff;
        color: var(--green-700);
    }
    .page-hero .btn-brand:hover,
    .page-hero .btn-brand:focus-visible {
        background: var(--mint);
        border-color: var(--mint);
        color: var(--green-700);
        box-shadow: 0 8px 18px rgba(0, 0, 0, .18);
    }

    /* ===== Statistiques ===== */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .9rem;
        margin-bottom: 1.25rem;
    }
    .stat-card {
        display: flex;
        align-items: center;
        gap: .85rem;
        padding: .95rem 1.1rem;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 14px;
        transition: border-color .2s ease, transform .2s ease, box-shadow .2s ease;
    }
    .stat-card:hover {
        border-color: var(--green-500);
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(16, 185, 129, .12);
    }
    .stat-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: var(--mint);
        color: var(--green-700);
        font-size: 1.05rem;
        flex-shrink: 0;
    }
    .stat-value {
        display: block;
        font-size: 1.4rem;
        font-weight: 800;
        line-height: 1.1;
        color: var(--ink);
    }
    .stat-label {
        display: block;
        font-size: .78rem;
        color: var(--muted);
    }
    .stat-card.is-pending   .stat-icon { background: #fef3c7; color: #92400e; }
    .stat-card.is-shipped   .stat-icon { background: #dbeafe; color: #1e40af; }
    .stat-card.is-delivered .stat-icon { background: #dcfce7; color: #166534; }

    /* ===== Boutons ===== */
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
    .link-clear { color: var(--muted); text-decoration: none; }
    .link-clear:hover { color: var(--ink); text-decoration: underline; }

    .soft-alert { border: 0; border-radius: 12px; background: var(--mint); color: var(--green-700); }

    /* ===== Filtres ===== */
    .filter-bar {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
        gap: 1rem;
        align-items: end;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--radius);
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
    }
    .filter-bar .form-label {
        font-size: .8rem;
        font-weight: 600;
        color: var(--muted);
        margin-bottom: .3rem;
    }
    .filter-bar .form-control,
    .filter-bar .input-group-text {
        border-color: var(--line);
        border-radius: 10px;
    }
    .filter-bar .input-group .form-control { border-top-left-radius: 0; border-bottom-left-radius: 0; }
    .filter-bar .input-group-text { background: var(--mint); color: var(--green-700); border-top-right-radius: 0; border-bottom-right-radius: 0; }
    .filter-bar .form-control:focus {
        border-color: var(--green-500);
        box-shadow: 0 0 0 .2rem rgba(16, 185, 129, .18);
    }
    .filter-actions { display: flex; align-items: center; gap: .5rem; }

    /* ===== Compteur ===== */
    .result-meta {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .5rem;
        margin-bottom: 1rem;
        color: var(--muted);
        font-size: .9rem;
    }
    .result-meta strong { color: var(--ink); }

    /* ===== Carte ===== */
    .order-card {
        height: 100%;
        display: flex;
        flex-direction: column;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--radius);
        overflow: hidden;
        transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    }
    .order-card:hover {
        border-color: var(--green-500);
        transform: translateY(-4px);
        box-shadow: 0 14px 28px rgba(16, 185, 129, .16);
    }
    .order-media {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        aspect-ratio: 4 / 3;
        background: var(--mint);
        color: var(--green-500);
        font-size: 2.5rem;
        overflow: hidden;
    }
    .order-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .4s ease;
    }
    .order-card:hover .order-media img { transform: scale(1.05); }
    .order-media .status-pill {
        position: absolute;
        top: .7rem;
        left: .7rem;
        box-shadow: 0 4px 10px rgba(0, 0, 0, .12);
    }

    .order-body {
        display: flex;
        flex-direction: column;
        flex: 1;
        padding: 1rem 1.1rem 1.1rem;
    }
    .order-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        margin-bottom: .55rem;
    }
    .order-date {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .78rem;
        color: var(--muted);
    }

    .order-title {
        font-size: 1.05rem;
        font-weight: 700;
        line-height: 1.3;
        margin: 0 0 .25rem;
        color: var(--ink);
    }
    .order-ref {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        margin: 0 0 .9rem;
        font-size: .82rem;
        color: var(--muted);
    }

    .order-meta {
        margin: 0 0 1rem;
        border-top: 1px solid var(--line);
    }
    .order-meta > div {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding: .5rem 0;
        border-bottom: 1px solid var(--line);
        font-size: .875rem;
    }
    .order-meta dt { font-weight: 400; color: var(--muted); }
    .order-meta dd { margin: 0; font-weight: 600; text-align: right; }
    .order-meta dd.is-price { color: var(--green-700); font-weight: 800; }

    /* ===== Statut ===== */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .3rem .7rem;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 700;
        background: #eef1ef;
        color: #44554c;
        white-space: nowrap;
    }
    .status-pill::before {
        content: "";
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
    }
    .status-pill.is-pending   { background: #fef3c7; color: #92400e; }
    .status-pill.is-confirmed { background: #d1fae5; color: #065f46; }
    .status-pill.is-shipped   { background: #dbeafe; color: #1e40af; }
    .status-pill.is-delivered { background: #dcfce7; color: #166534; }
    .status-pill.is-cancelled { background: #fee2e2; color: #991b1b; }

    /* ===== Vide ===== */
    .empty-state {
        text-align: center;
        padding: 3.5rem 1.5rem;
        background: #fff;
        border: 1px dashed var(--line);
        border-radius: var(--radius);
    }
    .empty-state > i { font-size: 2.5rem; color: var(--green-500); margin-bottom: 1rem; }
    .empty-state h2 { font-size: 1.2rem; font-weight: 700; margin-bottom: .35rem; }
    .empty-state p { color: var(--muted); margin-bottom: 1.25rem; }

    /* ===== Pagination ===== */
    .pagination-modern { gap: 6px; flex-wrap: wrap; margin: 0; }
    .pagination-modern .page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 42px;
        padding: .5rem .9rem;
        border: 1px solid var(--line);
        border-radius: 10px !important;
        color: var(--green-700);
        font-weight: 600;
        font-size: .9rem;
        background: #fff;
        transition: background .2s ease, color .2s ease, border-color .2s ease;
    }
    .pagination-modern .page-link:hover,
    .pagination-modern .page-link:focus-visible {
        background: var(--mint);
        border-color: var(--green-500);
        color: var(--green-700);
    }
    .pagination-modern .page-item.active .page-link {
        background: var(--green-700);
        border-color: var(--green-700);
        color: #fff;
    }
    .pagination-modern .page-item.disabled .page-link {
        background: #f6f8f7;
        color: #aab5af;
        border-color: var(--line);
    }

    /* ===== Entrée douce ===== */
    @media (prefers-reduced-motion: no-preference) {
        .order-card { animation: cardIn .45s ease-out both; }
        .col-12:nth-child(2) .order-card { animation-delay: .04s; }
        .col-12:nth-child(3) .order-card { animation-delay: .08s; }
        .col-12:nth-child(4) .order-card { animation-delay: .12s; }
        .col-12:nth-child(n+5) .order-card { animation-delay: .16s; }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: none; }
        }
    }
    @media (prefers-reduced-motion: reduce) {
        * { transition: none !important; }
    }

    /* ===== Responsive ===== */
    @media (max-width: 991.98px) {
        .stats-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .filter-bar { grid-template-columns: 1fr 1fr; }
        .filter-field:first-child { grid-column: 1 / -1; }
        .filter-actions { grid-column: 1 / -1; }
    }
    @media (max-width: 575.98px) {
        .page-hero { padding: 1.4rem; }
        .page-hero .btn { width: 100%; }
        .filter-bar { grid-template-columns: 1fr; padding: 1rem; }
        .filter-actions .btn-brand { flex: 1; }
        .pagination-modern .page-link { min-width: 36px; padding: .4rem .65rem; font-size: .82rem; }
    }
</style>
@endsection