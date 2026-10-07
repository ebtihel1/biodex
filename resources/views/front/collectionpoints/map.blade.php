<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biodex — Carte des Points de Collecte</title>

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Leaflet -->
    <link href="{{ asset('vendor/leaflet/leaflet.css') }}" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: system-ui, sans-serif;
            background: #f4f7f5;
        }

        .navbar {
            position: relative;
            background-color: #198754 !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            z-index: 1100;
        }

        .navbar-brand img {
            height: 32px;
        }

        .nav-link {
            color: #fff !important;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #a7f3d0 !important;
            text-decoration: underline;
        }

        .page-head {
            padding: 1.5rem 0 0.75rem;
        }

        .page-head h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #153f3a;
            margin: 0;
        }

        .page-head p {
            color: #68817c;
            margin: 0.25rem 0 0;
        }

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

        /* Legend overlay */
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

        /* Leaflet popup styling */
        .leaflet-popup-content {
            min-width: 210px;
        }

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

        .popup-tags {
            margin-top: 8px;
        }

        .popup-tags .badge {
            font-size: 0.68rem;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <!-- Navigation (identique au reste du front-office) -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center bg-white rounded-3 px-2 py-1" href="{{ url('/') }}">
                <img src="{{ asset('images/biodex-logo.png') }}" alt="Biodex">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a href="{{ url('/biodex') }}" class="nav-link"><i class="bi bi-house-door me-1"></i>Home</a></li>
                    <li class="nav-item"><a href="{{ route('front.collectionpoints.index') }}" class="nav-link"><i class="bi bi-grid me-1"></i>Liste</a></li>
                    <li class="nav-item"><a href="{{ route('front.collectionpoints.map') }}" class="nav-link active"><i class="bi bi-map me-1"></i>Carte</a></li>
                    <li class="nav-item"><a href="{{ url('/about') }}" class="nav-link"><i class="bi bi-info-circle me-1"></i>About</a></li>
                    <li class="nav-item"><a href="{{ url('/login') }}" class="nav-link text-light"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="page-head">
            <h1><i class="bi bi-map me-2" style="color:#198754"></i>Carte interactive des points de collecte</h1>
            <p>Niveau de remplissage <strong>prédit</strong> pour demain, calculé à partir de l'historique de dépôt de chaque point.</p>
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
                <div>
                    <button id="refreshMap" type="button" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-arrow-clockwise me-1"></i>Actualiser
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

    <footer style="background-color:#198754; color:#fff; text-align:center; padding:10px 0;">
        &copy; 2024 Biodex. All rights reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
                alert('Impossible de charger les prévisions. Le service est peut-être indisponible.');
            }
        }

        document.getElementById('refreshMap').addEventListener('click', loadForecasts);
        loadForecasts();
    })();
    </script>
</body>
</html>