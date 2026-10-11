{{-- resources/views/front/donations/index.blade.php --}}
@extends('front.layout')

@section('content')
@php
    // Conserve les filtres dans les liens de pagination
    $donations->withQueryString();

    $statusMap = [
        'available' => ['label' => 'Available', 'class' => 'is-available'],
        'claimed'   => ['label' => 'Claimed',   'class' => 'is-claimed'],
        'completed' => ['label' => 'Completed', 'class' => 'is-completed'],
        'cancelled' => ['label' => 'Cancelled', 'class' => 'is-cancelled'],
    ];

    $totals = [
        'total'     => (int) ($summary->total_count ?? $donations->total()),
        'available' => (int) ($summary->available_count ?? 0),
        'claimed'   => (int) ($summary->claimed_count ?? 0),
        'completed' => (int) ($summary->completed_count ?? 0),
    ];

    $hasFilters = request()->filled('date_from') || request()->filled('date_to') || request()->filled('item_search');
@endphp

<div class="container py-4 py-lg-5 donations-page">

    {{-- ===== Bannière ===== --}}
    <section class="page-hero">
        <div class="page-hero__text">
            <span class="page-hero__eyebrow"><i class="fas fa-recycle"></i> My impact</span>
            <h1 class="page-hero__title">My donations</h1>
            <p class="page-hero__subtitle">Track the items you've given a second life.</p>
        </div>
        <a href="{{ route('front.donations.create') }}" class="btn btn-brand">
            <i class="fas fa-plus me-2"></i>New donation
        </a>
    </section>

    {{-- ===== Statistiques ===== --}}
    <section class="stats-row" aria-label="Donation summary">
        <div class="stat-card">
            <span class="stat-icon"><i class="fas fa-box"></i></span>
            <div>
                <span class="stat-value">{{ number_format($totals['total']) }}</span>
                <span class="stat-label">Total donations</span>
            </div>
        </div>
        <div class="stat-card is-available">
            <span class="stat-icon"><i class="fas fa-circle-check"></i></span>
            <div>
                <span class="stat-value">{{ number_format($totals['available']) }}</span>
                <span class="stat-label">Available</span>
            </div>
        </div>
        <div class="stat-card is-claimed">
            <span class="stat-icon"><i class="fas fa-hand-holding-heart"></i></span>
            <div>
                <span class="stat-value">{{ number_format($totals['claimed']) }}</span>
                <span class="stat-label">Claimed</span>
            </div>
        </div>
        <div class="stat-card is-completed">
            <span class="stat-icon"><i class="fas fa-award"></i></span>
            <div>
                <span class="stat-value">{{ number_format($totals['completed']) }}</span>
                <span class="stat-label">Completed</span>
            </div>
        </div>
    </section>

    {{-- ===== Flash ===== --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show soft-alert" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ===== Filtres ===== --}}
    <form method="GET" action="{{ route('front.donations.index') }}" class="filter-bar" role="search">
        <div class="filter-field">
            <label for="item_search" class="form-label">Item</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" name="item_search" id="item_search" class="form-control"
                       value="{{ request('item_search') }}" placeholder="Search by item name">
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
                <a href="{{ route('front.donations.index') }}" class="btn btn-link link-clear">Clear</a>
            @endif
        </div>
    </form>

    {{-- ===== Compteur ===== --}}
    @unless($donations->isEmpty())
        <div class="result-meta">
            <span><strong>{{ $donations->total() }}</strong> {{ Str::plural('donation', $donations->total()) }}</span>
            <span>Showing {{ $donations->firstItem() }}–{{ $donations->lastItem() }}</span>
        </div>
    @endunless

    {{-- ===== Grille ===== --}}
    <div class="row g-3 g-lg-4">
        @forelse($donations as $donation)
            @php
                $statusKey = $donation->status->value ?? $donation->status;
                $status    = $statusMap[$statusKey] ?? ['label' => ucfirst($statusKey), 'class' => 'is-default'];
                $hasImage  = $donation->images && is_array($donation->images) && count($donation->images) > 0;
            @endphp

            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <article class="donation-card">
                    <a href="{{ route('front.donations.show', $donation) }}" class="donation-media" tabindex="-1" aria-hidden="true">
                        @if($hasImage)
                            <img src="{{ Storage::url($donation->images[0]) }}"
                                 alt="" loading="lazy">
                        @else
                            <i class="fas fa-recycle"></i>
                        @endif
                        <span class="status-pill {{ $status['class'] }}">{{ $status['label'] }}</span>
                    </a>

                    <div class="donation-body">
                        <div class="donation-top">
                            <time class="donation-date" datetime="{{ $donation->created_at->toDateString() }}">
                                <i class="far fa-calendar"></i>{{ $donation->created_at->format('M d, Y') }}
                            </time>
                        </div>

                        <h2 class="donation-title" title="{{ $donation->item_name }}">
                            {{ Str::limit($donation->item_name, 40) }}
                        </h2>
                        <p class="donation-donor">
                            <i class="fas fa-user-circle"></i>{{ $donation->user->name ?? 'Anonymous' }}
                        </p>

                        <dl class="donation-meta">
                            <div>
                                <dt>Category</dt>
                                <dd>{{ $donation->waste->category->name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt>Condition</dt>
                                <dd>{{ ucfirst($donation->condition) }}</dd>
                            </div>
                        </dl>

                        <a href="{{ route('front.donations.show', $donation) }}" class="btn btn-brand-outline btn-sm w-100 mt-auto">
                            View details <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-recycle"></i>
                    @if($hasFilters)
                        <h2>No donations match your filters</h2>
                        <p>Try a wider date range or a different search term.</p>
                        <a href="{{ route('front.donations.index') }}" class="btn btn-brand-outline">Clear filters</a>
                    @else
                        <h2>No donations yet</h2>
                        <p>Add your first item and it will show up here.</p>
                        <a href="{{ route('front.donations.create') }}" class="btn btn-brand">
                            <i class="fas fa-plus me-2"></i>Make your first donation
                        </a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    {{-- ===== Pagination ===== --}}
    @if($donations->hasPages())
        <nav class="mt-5" aria-label="Donations pagination">
            <ul class="pagination pagination-modern justify-content-center">
                <li class="page-item {{ $donations->onFirstPage() ? 'disabled' : '' }}">
                    @if($donations->onFirstPage())
                        <span class="page-link"><i class="fas fa-chevron-left me-1"></i>Previous</span>
                    @else
                        <a class="page-link" href="{{ $donations->previousPageUrl() }}" rel="prev">
                            <i class="fas fa-chevron-left me-1"></i>Previous
                        </a>
                    @endif
                </li>

                @foreach ($donations->getUrlRange(1, $donations->lastPage()) as $page => $url)
                    <li class="page-item {{ $page == $donations->currentPage() ? 'active' : '' }}">
                        @if($page == $donations->currentPage())
                            <span class="page-link" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    </li>
                @endforeach

                <li class="page-item {{ $donations->hasMorePages() ? '' : 'disabled' }}">
                    @if($donations->hasMorePages())
                        <a class="page-link" href="{{ $donations->nextPageUrl() }}" rel="next">
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
    .donations-page {
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
    .stat-card.is-available .stat-icon { background: #dcfce7; color: #166534; }
    .stat-card.is-claimed   .stat-icon { background: #dbeafe; color: #1e40af; }
    .stat-card.is-completed .stat-icon { background: #cffafe; color: #155e75; }

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
    .donation-card {
        height: 100%;
        display: flex;
        flex-direction: column;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--radius);
        overflow: hidden;
        transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    }
    .donation-card:hover {
        border-color: var(--green-500);
        transform: translateY(-4px);
        box-shadow: 0 14px 28px rgba(16, 185, 129, .16);
    }
    .donation-media {
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
    .donation-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .4s ease;
    }
    .donation-card:hover .donation-media img { transform: scale(1.05); }
    .donation-media .status-pill {
        position: absolute;
        top: .7rem;
        left: .7rem;
        box-shadow: 0 4px 10px rgba(0, 0, 0, .12);
    }

    .donation-body {
        display: flex;
        flex-direction: column;
        flex: 1;
        padding: 1rem 1.1rem 1.1rem;
    }
    .donation-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        margin-bottom: .55rem;
    }
    .donation-date {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .78rem;
        color: var(--muted);
    }

    .donation-title {
        font-size: 1.05rem;
        font-weight: 700;
        line-height: 1.3;
        margin: 0 0 .25rem;
        color: var(--ink);
    }
    .donation-donor {
        display: flex;
        align-items: center;
        gap: .4rem;
        margin: 0 0 .9rem;
        font-size: .85rem;
        color: var(--muted);
    }
    .donation-donor i { color: var(--green-500); }

    .donation-meta {
        margin: 0 0 1rem;
        border-top: 1px solid var(--line);
    }
    .donation-meta > div {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding: .5rem 0;
        border-bottom: 1px solid var(--line);
        font-size: .875rem;
    }
    .donation-meta dt { font-weight: 400; color: var(--muted); }
    .donation-meta dd { margin: 0; font-weight: 600; text-align: right; }

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
    .status-pill.is-available { background: #dcfce7; color: #166534; }
    .status-pill.is-claimed   { background: #dbeafe; color: #1e40af; }
    .status-pill.is-completed { background: #cffafe; color: #155e75; }
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
        .donation-card { animation: cardIn .45s ease-out both; }
        .col-12:nth-child(2) .donation-card { animation-delay: .04s; }
        .col-12:nth-child(3) .donation-card { animation-delay: .08s; }
        .col-12:nth-child(4) .donation-card { animation-delay: .12s; }
        .col-12:nth-child(n+5) .donation-card { animation-delay: .16s; }
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
