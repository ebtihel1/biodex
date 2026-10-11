{{-- resources/views/front/collectionpoints/index.blade.php --}}
@extends('front.layout')

@section('title', 'Points de Collecte')

@push('head')
    <link href="{{ asset('vendor/leaflet/leaflet.css') }}" rel="stylesheet">
    <style>
        html { scroll-behavior: smooth; }
        #interactive-map-section { scroll-margin-top: 90px; }
    </style>
@endpush

@section('hero')
    <section class="hero-header">
        <div class="hero-content text-start">
            <h1>Transform our <span>waste</span> into resources</h1>
            <p class="lead mt-3">Find the nearest collection point and contribute to a cleaner environment.</p>
            <a href="#interactive-map-section" class="btn btn-success btn-lg mt-4">
                Find a collection point
            </a>
        </div>
    </section>
@endsection

@section('content')
<div class="cp-page">
    <div class="container pb-4">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show soft-alert" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($collectionPoints->isEmpty())
            <div class="empty-state">
                <i class="fas fa-map-location-dot"></i>
                <h2>No collection points found</h2>
                <p>There are currently no active collection points in your area.</p>
            </div>
        @else
            <div class="result-meta">
                <span><i class="fas fa-location-dot me-2"></i><strong>{{ $collectionPoints->total() }}</strong>
                    {{ Str::plural('collection point', $collectionPoints->total()) }} found</span>
                <a href="#interactive-map-section" class="btn btn-brand-outline btn-sm">
                    <i class="fas fa-map-marked-alt me-1"></i>View interactive map
                </a>
            </div>

            <div class="row g-3 g-lg-4">
                @foreach ($collectionPoints as $collectionPoint)
                    @php $isActive = $collectionPoint->status === 'active'; @endphp
                    <div class="col-12 col-md-6 col-lg-4">
                        <article class="cp-card">
                            <div class="cp-card__head">
                                <span class="cp-avatar"><i class="fas fa-recycle"></i></span>
                                <span class="status-pill {{ $isActive ? 'is-active' : 'is-inactive' }}">
                                    {{ ucfirst($collectionPoint->status) }}
                                </span>
                            </div>

                            <div class="cp-card__body">
                                <h3 class="cp-name">{{ $collectionPoint->name }}</h3>

                                <div class="cp-location">
                                    <i class="fas fa-location-dot"></i>
                                    <div>
                                        <p class="cp-address">{{ $collectionPoint->address }}</p>
                                        <p class="cp-city">{{ $collectionPoint->city }}</p>
                                    </div>
                                </div>

                                <div class="cp-foot">
                                    <span class="cp-hours"><i class="far fa-clock"></i>Open until 6 PM</span>
                                    <a href="{{ route('front.collectionpoints.show', $collectionPoint->id) }}"
                                       class="btn btn-brand btn-sm">
                                        Details <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>

            @if ($collectionPoints->hasPages())
                <div class="d-flex flex-wrap justify-content-between align-items-center mt-4 gap-2">
                    <div class="text-muted small">
                        Showing {{ $collectionPoints->firstItem() }}–{{ $collectionPoints->lastItem() }}
                        of {{ $collectionPoints->total() }} entries
                    </div>
                    <ul class="pagination pagination-modern mb-0">
                        <li class="page-item {{ $collectionPoints->onFirstPage() ? 'disabled' : '' }}">
                            @if ($collectionPoints->onFirstPage())
                                <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                            @else
                                <a class="page-link" href="{{ $collectionPoints->previousPageUrl() }}" rel="prev"><i class="fas fa-chevron-left"></i></a>
                            @endif
                        </li>
                        @foreach ($collectionPoints->getUrlRange(1, $collectionPoints->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $collectionPoints->currentPage() ? 'active' : '' }}">
                                @if ($page == $collectionPoints->currentPage())
                                    <span class="page-link" aria-current="page">{{ $page }}</span>
                                @else
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                @endif
                            </li>
                        @endforeach
                        <li class="page-item {{ $collectionPoints->hasMorePages() ? '' : 'disabled' }}">
                            @if ($collectionPoints->hasMorePages())
                                <a class="page-link" href="{{ $collectionPoints->nextPageUrl() }}" rel="next"><i class="fas fa-chevron-right"></i></a>
                            @else
                                <span class="page-link"><i class="fas fa-chevron-right"></i></span>
                            @endif
                        </li>
                    </ul>
                </div>
            @endif

            {{-- ============ CARTE INTERACTIVE AI ============ --}}
            <div id="interactive-map-section" class="mt-5">
                <div class="result-meta">
                    <span>
                        <i class="fas fa-map-marked-alt me-2"></i>
                        Interactive Map — Predicted Fill Level for Tomorrow
                    </span>
                    <button id="refreshMap" type="button" class="btn btn-brand-outline btn-sm">
                        <i class="fas fa-rotate me-1"></i>Refresh
                    </button>
                </div>

                <div class="map-card position-relative">
                    <div class="map-toolbar">
                        <div class="map-stats" id="mapStats">
                            <span><i class="fas fa-location-dot me-1 text-success"></i><b id="totalCount">0</b> points</span>
                            <span class="stat"><i class="stat-dot me-1" style="background:#198754"></i><b id="lowCount">0</b> faible</span>
                            <span class="stat"><i class="stat-dot me-1" style="background:#f59f00"></i><b id="moderateCount">0</b> moyen</span>
                            <span class="stat"><i class="stat-dot me-1" style="background:#dc3545"></i><b id="highCount">0</b> élevé</span>
                            <span class="stat"><i class="stat-dot me-1" style="background:#adb5bd"></i><b id="insufficientCount">0</b> données insuffisantes</span>
                        </div>
                    </div>

                    <div id="collectionMap"></div>

                    <div class="map-legend">
                        <h6>Niveau de remplissage prédit</h6>
                        <div class="legend-row"><i class="legend-dot" style="background:#198754"></i>Faible (&lt; 50%)</div>
                        <div class="legend-row"><i class="legend-dot" style="background:#f59f00"></i>Moyen (50 – 79%)</div>
                        <div class="legend-row"><i class="legend-dot" style="background:#dc3545"></i>Élevé (≥ 80%)</div>
                        <div class="legend-row"><i class="legend-dot" style="background:#adb5bd"></i>Données insuffisantes</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
<script>
(() => {
    const levelColors = {low: '#198754', moderate: '#f59f00', high: '#dc3545', insufficient: '#adb5bd'};
    const levelLabels = {low: 'Faible', moderate: 'Moyen', high: 'Élevé', insufficient: 'Données insuffisantes'};

    const map = L.map('collectionMap').setView([35.9, 10.3], 7);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    const markers = [];
    const counts = {total: 0, low: 0, moderate: 0, high: 0, insufficient: 0};

    function fillColorFor(level) {
        return levelColors[level] || levelColors.insufficient;
    }

    function renderPoint(point) {
        const color = fillColorFor(point.level);
        const isActive = point.status === 'active';

        const marker = L.circleMarker([point.latitude, point.longitude], {
            radius: isActive ? 14 : 9,
            color: '#ffffff',
            weight: 2,
            fillColor: color,
            fillOpacity: isActive ? 0.85 : 0.35
        });

        let fillHtml = point.ratio_pct !== null
            ? `<div class="fill-bar"><div style="width:${Math.min(100, point.ratio_pct)}%;background:${color}"></div></div>`
            : '';

        let metaHtml = point.ratio_pct !== null
            ? `<div class="popup-meta">
                 <strong>${Math.round(point.ratio_pct)}%</strong> de la capacité estimée
                 <small>${point.predicted_volume_kg} kg / ${point.capacity_kg} kg prévus</small>
                 <small>Date prévision : ${point.forecast_date} · confiance ${Math.round((point.confidence ?? 0) * 100)}%</small>
               </div>`
            : `<div class="popup-meta"><small>Pas assez d'historique pour une prévision fiable (${point.training_days ?? 0} jour(s)).</small></div>`;

        const tags = (point.accepted_categories || []).length
            ? `<div class="popup-tags">${point.accepted_categories.map(c => `<span class="badge bg-light text-dark border me-1">${c}</span>`).join('')}</div>`
            : '';

        marker.bindPopup(`
            <div class="popup-title">${point.name}</div>
            <div class="popup-address">${point.address || ''}${point.city ? ' — ' + point.city : ''}</div>
            <span class="badge ${isActive ? 'bg-success' : 'bg-secondary'} mb-1">${point.status === 'active' ? 'Actif' : 'Inactif'}</span>
            <div class="popup-meta mt-1">Niveau prédit : <strong>${levelLabels[point.level]}</strong></div>
            ${fillHtml}
            ${metaHtml}
            ${tags}
        `);

        marker.on('click', () => marker.openPopup());
        markers.push(marker);
        marker.addTo(map);

        counts.total += 1;
        counts[point.level] = (counts[point.level] || 0) + 1;
    }

    function updateStats() {
        document.getElementById('totalCount').textContent = counts.total;
        document.getElementById('lowCount').textContent = counts.low;
        document.getElementById('moderateCount').textContent = counts.moderate;
        document.getElementById('highCount').textContent = counts.high;
        document.getElementById('insufficientCount').textContent = counts.insufficient;
    }

    async function loadForecasts() {
        try {
            const response = await fetch('{{ route('collection-ai.forecasts') }}', {headers: {Accept: 'application/json'}});
            if (!response.ok) throw new Error('HTTP ' + response.status);
            const data = await response.json();
            const points = Array.isArray(data.points) ? data.points : [];

            markers.forEach(marker => map.removeLayer(marker));
            markers.length = 0;
            Object.keys(counts).forEach(key => counts[key] = 0);

            points.forEach(renderPoint);
            updateStats();

            if (markers.length > 1) {
                map.fitBounds(L.latLngBounds(markers.map(m => m.getLatLng())).pad(0.15));
            } else if (markers.length === 1) {
                map.setView(markers[0].getLatLng(), 13);
            }
        } catch (error) {
            console.error('Impossible de charger les prévisions :', error);
        }
    }

    document.getElementById('refreshMap').addEventListener('click', loadForecasts);
    loadForecasts();
})();
</script>

<style>
    .cp-page {
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

    .result-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: .5rem;
        margin-bottom: 1.25rem;
        color: var(--muted);
        font-size: .92rem;
    }
    .result-meta strong { color: var(--ink); }
    .soft-alert { border: 0; border-radius: 12px; background: var(--mint); color: var(--green-700); }

    /* ===== Carte point de collecte ===== */
    .cp-card {
        height: 100%;
        display: flex;
        flex-direction: column;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--radius);
        overflow: hidden;
        transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    }
    .cp-card:hover {
        border-color: var(--green-500);
        transform: translateY(-4px);
        box-shadow: 0 14px 28px rgba(16, 185, 129, .16);
    }
    .cp-card__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding: 1rem 1.15rem;
        background: linear-gradient(120deg, #047857 0%, #059669 60%, #10b981 100%);
    }
    .cp-avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: rgba(255, 255, 255, .18);
        color: #fff;
        font-size: 1.05rem;
    }
    .cp-card__body {
        display: flex;
        flex-direction: column;
        flex: 1;
        padding: 1.1rem 1.15rem 1.15rem;
    }
    .cp-name {
        font-size: 1.12rem;
        font-weight: 700;
        line-height: 1.3;
        margin: 0 0 .85rem;
    }
    .cp-location {
        display: flex;
        gap: .65rem;
        margin-bottom: 1.1rem;
    }
    .cp-location > i { color: var(--green-500); margin-top: .15rem; }
    .cp-address { margin: 0; font-weight: 600; font-size: .92rem; }
    .cp-city { margin: 0; color: var(--muted); font-size: .85rem; }

    .cp-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        margin-top: auto;
        padding-top: 1rem;
        border-top: 1px solid var(--line);
    }
    .cp-hours {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .8rem;
        color: var(--muted);
    }

    /* ===== Statut ===== */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .28rem .7rem;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 700;
        white-space: nowrap;
        background: rgba(255, 255, 255, .92);
        color: #44554c;
    }
    .status-pill::before { content: ""; width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
    .status-pill.is-active   { color: #065f46; }
    .status-pill.is-inactive { color: #991b1b; }

    /* ===== Boutons ===== */
    .btn-brand {
        background: var(--green-700);
        border: 1px solid var(--green-700);
        color: #fff;
        font-weight: 600;
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
    .empty-state p { color: var(--muted); margin-bottom: 0; }

    /* ===== Pagination ===== */
    .pagination-modern { gap: 6px; flex-wrap: wrap; }
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
    }
    .pagination-modern .page-link:hover,
    .pagination-modern .page-link:focus-visible {
        background: var(--mint);
        border-color: var(--green-500);
        color: var(--green-700);
    }
    .pagination-modern .page-item.active .page-link { background: var(--green-700); border-color: var(--green-700); color: #fff; }
    .pagination-modern .page-item.disabled .page-link { background: #f6f8f7; color: #aab5af; border-color: var(--line); }

    /* ===== Map ===== */
    .map-card {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid var(--line);
        box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
        background: #fff;
    }
    #collectionMap { height: 62vh; min-height: 420px; width: 100%; background: #e8ecea; }
    .map-legend {
        position: absolute;
        right: 16px;
        bottom: 16px;
        z-index: 900;
        background: #fff;
        border-radius: 10px;
        padding: 10px 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.18);
        font-size: 0.8rem;
        min-width: 160px;
    }
    .map-legend h6 {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #68817c;
        margin-bottom: 6px;
    }
    .legend-dot { display: inline-block; width: 12px; height: 12px; border-radius: 50%; margin-right: 6px; vertical-align: middle; border: 2px solid rgba(0,0,0,0.15); }
    .legend-row { display: flex; align-items: center; margin-bottom: 3px; }
    .map-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.9rem 1rem;
        border-bottom: 1px solid var(--line);
        flex-wrap: wrap;
    }
    .map-stats { display: flex; gap: 1.25rem; font-size: 0.85rem; color: #153f3a; flex-wrap: wrap; }
    .map-stats .stat { display: inline-flex; align-items: center; gap: 0.35rem; }
    .stat-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
    .leaflet-popup-content { min-width: 210px; }
    .popup-title { font-weight: 700; color: #153f3a; font-size: 0.95rem; margin-bottom: 2px; }
    .popup-address { color: #68817c; font-size: 0.78rem; margin-bottom: 8px; }
    .fill-bar { height: 10px; background: #e9ecef; border-radius: 6px; overflow: hidden; margin: 6px 0 8px; }
    .fill-bar > div { height: 100%; border-radius: 6px; }
    .popup-meta { font-size: 0.78rem; color: #153f3a; line-height: 1.6; }
    .popup-meta small { display: block; color: #68817c; }
    .popup-tags { margin-top: 8px; }
    .popup-tags .badge { font-size: 0.68rem; font-weight: 500; }

    @media (prefers-reduced-motion: no-preference) {
        .cp-card { animation: cardIn .45s ease-out both; }
        .cp-card:hover { animation: none; }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: none; }
        }
    }
    @media (prefers-reduced-motion: reduce) { * { transition: none !important; } }

    @media (max-width: 575.98px) {
        .result-meta .btn { width: 100%; }
        .pagination-modern .page-link { min-width: 36px; padding: .4rem .65rem; font-size: .82rem; }
    }
</style>
@endsection
