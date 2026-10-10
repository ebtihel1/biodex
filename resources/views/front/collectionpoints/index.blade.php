<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biodex — Points de Collecte</title>

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet -->
    <link href="{{ asset('vendor/leaflet/leaflet.css') }}" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: system-ui, sans-serif;
            overflow-x: hidden;
        }

        /* Smooth scroll pour le bouton "View interactive map" */
        html { scroll-behavior: smooth; }
        #interactive-map-section { scroll-margin-top: 90px; }

        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            transition: background-color 0.4s ease, box-shadow 0.3s ease;
            background-color: transparent !important;
            z-index: 1000;
        }

        .navbar.scrolled {
            background-color: #198754 !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .navbar-brand {
            font-weight: bold;
            color: #fff !important;
        }

        .nav-link {
            color: #fff !important;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            text-decoration: underline;
            color: #a7f3d0 !important;
        }

        .hero-header {
            height: 80vh;
            background: url("{{ asset('images/Earth.png') }}") no-repeat center center/cover;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            color: white;
            padding-left: 100px;
        }

        .hero-header::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 600px;
        }

        .hero-content h1 {
            font-size: 2.8rem;
            font-weight: bold;
        }

        .hero-content span {
            color: #a7f3d0;
        }

        main {
            background: linear-gradient(120deg, #f8fff8, #f5fff2);
            padding-top: 80px;
        }

        footer {
            background-color: #198754;
            color: #fff;
            text-align: center;
            padding: 10px 0;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .collection-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }

        .collection-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .card-header {
            background-color: #198754;
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
        }

        .card-title {
            margin: 0;
        }

        .location-info {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .location-icon {
            color: #198754;
            margin-right: 10px;
            margin-top: 3px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-active {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .empty-state-icon {
            font-size: 4rem;
            color: #9ca3af;
            margin-bottom: 20px;
        }

        .results-count {
            background-color: #e8f5e8;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: #065f46;
            font-weight: 500;
        }

        /* ===== Pagination ===== */
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }
        .pagination-wrapper .pagination {
            gap: 0.35rem;
            margin-bottom: 0;
        }
        .pagination-wrapper .page-link {
            border: 1px solid rgba(25, 135, 84, 0.2);
            border-radius: 10px !important;
            color: #198754;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 0.5rem 0.85rem;
            transition: all 0.2s ease;
            min-width: 42px;
            text-align: center;
        }
        .pagination-wrapper .page-link:hover {
            background: #198754;
            color: #fff;
            border-color: #198754;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(25, 135, 84, 0.25);
        }
        .pagination-wrapper .page-item.active .page-link {
            background: linear-gradient(135deg, #198754, #146c43);
            border-color: #198754;
            color: #fff;
            box-shadow: 0 4px 12px rgba(25, 135, 84, 0.35);
        }
        .pagination-wrapper .page-item.disabled .page-link {
            background: #f8f9fa;
            border-color: #e9ecef;
            color: #adb5bd;
        }

        /* ===== Map section ===== */
        .map-card {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid rgba(18, 63, 55, 0.12);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            background: #fff;
        }

        #collectionMap {
            height: 62vh;
            min-height: 420px;
            width: 100%;
            background: #e8ecea;
        }

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

        .legend-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 6px;
            vertical-align: middle;
            border: 2px solid rgba(0,0,0,0.15);
        }

        .legend-row {
            display: flex;
            align-items: center;
            margin-bottom: 3px;
        }

        .map-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.9rem 1rem;
            border-bottom: 1px solid rgba(18, 63, 55, 0.1);
            flex-wrap: wrap;
        }

        .map-stats {
            display: flex;
            gap: 1.25rem;
            font-size: 0.85rem;
            color: #153f3a;
            flex-wrap: wrap;
        }

        .map-stats .stat {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .stat-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }

        .leaflet-popup-content { min-width: 210px; }

        .popup-title {
            font-weight: 700;
            color: #153f3a;
            font-size: 0.95rem;
            margin-bottom: 2px;
        }

        .popup-address {
            color: #68817c;
            font-size: 0.78rem;
            margin-bottom: 8px;
        }

        .fill-bar {
            height: 10px;
            background: #e9ecef;
            border-radius: 6px;
            overflow: hidden;
            margin: 6px 0 8px;
        }

        .fill-bar > div {
            height: 100%;
            border-radius: 6px;
        }

        .popup-meta {
            font-size: 0.78rem;
            color: #153f3a;
            line-height: 1.6;
        }

        .popup-meta small {
            display: block;
            color: #68817c;
        }

        .popup-tags { margin-top: 8px; }

        .popup-tags .badge {
            font-size: 0.68rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .hero-header {
                padding-left: 20px;
                padding-right: 20px;
                justify-content: center;
                text-align: center;
            }
            .hero-content h1 { font-size: 2rem; }
            #collectionMap { height: 400px; }
        }
    </style>
</head>

<body>
   @extends('front.navbar')
    <!-- Hero header -->
    <header class="hero-header">
        <div class="hero-content">
            <h1>Transform our <span>waste</span> into resources</h1>
            <p class="lead mt-3">Find the nearest collection point and contribute to a cleaner environment.</p>
            <button class="btn btn-success btn-lg mt-4">Find a collection point</button>
        </div>
    </header>

    <!-- Main content -->
    <main>
        <div class="container mb-5">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($collectionPoints->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h3 class="mb-3">No collection points found</h3>
                    <p class="text-muted mb-4">There are currently no active collection points in your area.</p>
                    <button class="btn btn-outline-success">Suggest a location</button>
                </div>
            @else
                <div class="results-count d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span>
                        <i class="fas fa-info-circle me-2"></i>
                        {{ $collectionPoints->total() }} collection point(s) found
                    </span>
                    <a href="#interactive-map-section" class="btn btn-success btn-sm">
                        <i class="fas fa-map-marked-alt me-1"></i> View interactive map
                    </a>
                </div>

                {{-- Grid of collection points --}}
                <div class="row">
                    @foreach ($collectionPoints as $collectionPoint)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="collection-card card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ $collectionPoint->name }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="location-info">
                                        <i class="fas fa-map-marker-alt location-icon"></i>
                                        <div>
                                            <p class="mb-1 fw-semibold">{{ $collectionPoint->address }}</p>
                                            <p class="mb-2 text-muted">{{ $collectionPoint->city }}</p>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="status-badge {{ $collectionPoint->status == 'active' ? 'status-active' : 'status-inactive' }}">
                                            <i class="fas fa-circle me-1" style="font-size: 0.6rem;"></i>
                                            {{ ucfirst($collectionPoint->status) }}
                                        </span>
                                        <span class="text-muted small">
                                            <i class="far fa-clock me-1"></i>
                                            Open until 6 PM
                                        </span>
                                    </div>

                                    <div class="d-grid">
                                        <a href="{{ route('front.collectionpoints.show', $collectionPoint->id) }}" class="btn btn-success">
                                            <i class="fas fa-info-circle me-2"></i>
                                            View details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- ============ PAGINATION ============ --}}
                @if ($collectionPoints->hasPages())
                    <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 gap-2">
                        <div class="text-muted small">
                            Showing {{ $collectionPoints->firstItem() }}–{{ $collectionPoints->lastItem() }}
                            of {{ $collectionPoints->total() }} entries
                        </div>
                        <div class="pagination-wrapper">
                            {{ $collectionPoints->withQueryString()->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif

                {{-- ============ CARTE INTERACTIVE AI EN BAS DE PAGE ============ --}}
                <div id="interactive-map-section" class="mt-5">
                    <div class="results-count d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span>
                            <i class="fas fa-map-marked-alt me-2"></i>
                            Carte interactive — Niveau de remplissage <strong>prédit</strong> pour demain
                        </span>
                        <button id="refreshMap" type="button" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-arrow-clockwise me-1"></i>Actualiser
                        </button>
                    </div>

                    <div class="map-card position-relative">
                        <div class="map-toolbar">
                            <div class="map-stats" id="mapStats">
                                <span><i class="bi bi-geo-alt me-1 text-success"></i><b id="totalCount">0</b> points</span>
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
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p class="mb-0">&copy; 2024 Biodex. All rights reserved.</p>
        </div>
    </footer>

    {{-- Bootstrap bundle --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Navbar scroll behavior --}}
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>

    {{-- Leaflet --}}
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
</body>
</html>