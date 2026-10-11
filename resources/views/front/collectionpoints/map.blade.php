{{-- resources/views/front/collectionpoints/map.blade.php --}}
@extends('front.layout')

@section('title', 'Carte des Points de Collecte')

@push('head')
    <link href="{{ asset('vendor/leaflet/leaflet.css') }}" rel="stylesheet">
@endpush

@section('hero')
    <section class="cp-map-hero">
        <div class="cp-map-hero__inner">
            <span class="cp-map-hero__eyebrow"><i class="fas fa-map"></i> Carte interactive</span>
            <h1>Carte des points de collecte</h1>
            <p>Niveau de remplissage <strong>prédit</strong> pour demain, calculé à partir de l'historique de dépôt de chaque point.</p>
        </div>
    </section>
@endsection

@section('content')
<div class="cp-map-page">
    <div class="container pb-4">
        <div class="map-card position-relative">
            <div class="map-toolbar">
                <div class="map-stats" id="mapStats">
                    <span><i class="fas fa-location-dot me-1 text-success"></i><b id="totalCount">0</b> points</span>
                    <span class="stat"><i class="stat-dot me-1" style="background:#198754"></i><b id="lowCount">0</b> faible</span>
                    <span class="stat"><i class="stat-dot me-1" style="background:#f59f00"></i><b id="moderateCount">0</b> moyen</span>
                    <span class="stat"><i class="stat-dot me-1" style="background:#dc3545"></i><b id="highCount">0</b> élevé</span>
                    <span class="stat"><i class="stat-dot me-1" style="background:#adb5bd"></i><b id="insufficientCount">0</b> données insuffisantes</span>
                </div>
                <div class="map-toolbar__actions">
                    <a href="{{ route('front.collectionpoints.index') }}" class="btn btn-brand-outline btn-sm">
                        <i class="fas fa-list me-1"></i>Voir la liste
                    </a>
                    <button id="refreshMap" type="button" class="btn btn-brand btn-sm">
                        <i class="fas fa-rotate me-1"></i>Actualiser
                    </button>
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
    .cp-map-page {
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

    .cp-map-hero {
        background:
            radial-gradient(120% 160% at 100% 0%, rgba(255,255,255,.22) 0%, rgba(255,255,255,0) 45%),
            linear-gradient(120deg, #047857 0%, #059669 55%, #10b981 100%);
        color: #fff;
        padding: 3rem 0 2.5rem;
    }
    .cp-map-hero__inner { max-width: 1160px; margin: 0 auto; padding: 0 1.5rem; }
    .cp-map-hero__eyebrow {
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
    .cp-map-hero h1 { font-size: clamp(1.6rem, 3vw, 2.1rem); font-weight: 800; margin: 0 0 .4rem; }
    .cp-map-hero p { margin: 0; opacity: .92; max-width: 720px; }

    .map-card {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid var(--line);
        box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
        background: #fff;
    }
    .map-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.9rem 1rem;
        border-bottom: 1px solid var(--line);
        flex-wrap: wrap;
    }
    .map-toolbar__actions { display: flex; gap: .5rem; flex-wrap: wrap; }
    .map-stats { display: flex; gap: 1.25rem; font-size: 0.85rem; color: #153f3a; flex-wrap: wrap; }
    .map-stats .stat { display: inline-flex; align-items: center; gap: 0.35rem; }
    .stat-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
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

    .leaflet-popup-content { min-width: 210px; }
    .popup-title { font-weight: 700; color: #153f3a; font-size: 0.95rem; margin-bottom: 2px; }
    .popup-address { color: #68817c; font-size: 0.78rem; margin-bottom: 8px; }
    .fill-bar { height: 10px; background: #e9ecef; border-radius: 6px; overflow: hidden; margin: 6px 0 8px; }
    .fill-bar > div { height: 100%; border-radius: 6px; }
    .popup-meta { font-size: 0.78rem; color: #153f3a; line-height: 1.6; }
    .popup-meta small { display: block; color: #68817c; }
    .popup-tags { margin-top: 8px; }
    .popup-tags .badge { font-size: 0.68rem; font-weight: 500; }

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
    .btn-brand-outline:hover, .btn-brand-outline:focus-visible { background: var(--green-700); color: #fff; }

    @media (max-width: 575.98px) {
        .map-toolbar__actions .btn { flex: 1; }
    }
</style>
@endsection
