<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collection Point Details - Biodex</title>

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        body {
            margin: 0;
            font-family: system-ui, sans-serif;
            overflow-x: hidden;
            background: linear-gradient(120deg, #f8fff8, #f5fff2);
        }

        /* Navbar identical to the first page */
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

        /* Enhanced Hero Header */
        .hero-header {
            height: 50vh;
            background: url("{{ asset('images/Earth.png') }}") no-repeat center center/cover;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            color: white;
            padding-left: 100px;
            margin-bottom: 2rem;
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
            padding-top: 80px;
        }

        /* Card styles identical to the first page */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .detail-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 2rem;
        }

        .detail-card:hover {
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
            display: flex;
            align-items: center;
        }

        .info-section { 
            padding: 1.5rem; 
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-item:last-child { 
            border-bottom: none; 
            margin-bottom: 0; 
            padding-bottom: 0; 
        }

        .info-icon {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin-right: 1rem; flex-shrink: 0;
        }

        .info-content { 
            flex: 1; 
        }

        .info-label { 
            font-weight: 600; 
            color: #333;
            margin-bottom: 0.3rem; 
        }

        .info-value { 
            color: #666; 
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

        .category-badge {
            display: inline-block;
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            margin: 0.2rem;
            font-size: 0.85rem;
            border: 1px solid rgba(25, 135, 84, 0.2);
        }

        .hours-list { 
            list-style: none; 
            padding-left: 0; 
            margin-bottom: 0; 
        }

        .hours-list li { 
            padding: 0.3rem 0; 
            border-bottom: 1px solid #f5f5f5; 
        }

        .hours-list li:last-child { 
            border-bottom: none; 
        }

        .map-container {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-top: 1rem;
            border: 1px solid #e0e0e0;
        }

        #map { 
            height: 300px; 
            width: 100%; 
        }

        .btn-success {
            background-color: #198754;
            border-color: #198754;
            border-radius: 8px;
            padding: 0.7rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-success:hover {
            background-color: #157347;
            border-color: #146c43;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        .btn-outline-secondary {
            border-radius: 8px;
            padding: 0.7rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-outline-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .last-updated {
            background-color: rgba(0, 0, 0, 0.03);
            border-radius: 8px;
            padding: 0.8rem;
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }
        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #f0f0f0;
        }

        footer {
            background-color: #198754;
            color: #fff;
            text-align: center;
            padding: 20px 0;
            margin-top: 3rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-header {
                padding-left: 20px;
                padding-right: 20px;
                justify-content: center;
                text-align: center;
                height: 40vh;
            }
            
            .hero-content h1 {
                font-size: 2rem;
            }

            .action-buttons { 
                flex-direction: column; 
                gap: 1rem; 
            }
            
            .action-buttons a { 
                width: 100%; 
                text-align: center; 
            }
        }
    </style>
</head>

<body>
    <!-- Identical navigation bar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="bi bi-recycle me-2"></i>
                Biodex
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                            <i class="bi bi-house-door me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('front.products.index') }}" class="nav-link {{ request()->routeIs('front.products.*') || request()->is('shop/products*') ? 'active' : '' }}">
                            <i class="bi bi-bag-check me-1"></i>Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/biodex/collectionpoints') }}" class="nav-link {{ request()->is('front.collectionpoints.index') ? 'active' : '' }}">
                            <i class="bi bi-geo-alt me-1"></i>Collection Points
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/campaigns') }}" class="nav-link {{ request()->is('campaigns*') ? 'active' : '' }}">
                            <i class="bi bi-megaphone me-1"></i>Campaigns
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('front.waste-categories.index') }}" class="nav-link {{ request()->is('categories*') ? 'active' : '' }}">
                            <i class="bi bi-grid-3x3-gap me-1"></i>Waste Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('front.wastes.index') }}" class="nav-link {{ request()->is('wastess*') ? 'active' : '' }}">
                            <i class="bi bi-trash me-1"></i>Waste
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/about') }}" class="nav-link {{ request()->is('about') ? 'active' : '' }}">
                            <i class="bi bi-info-circle me-1"></i>About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/contact') }}" class="nav-link {{ request()->is('contact') ? 'active' : '' }}">
                            <i class="bi bi-envelope me-1"></i>Contact
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/login') }}" class="nav-link text-light">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Enhanced hero header -->
    <header class="hero-header">
        <div class="hero-content">
            <h1>Details of the <span>Collection Point</span></h1>
            <p class="lead mt-3">Complete information about {{ $collectionPoint->name }}</p>
        </div>
    </header>

    <!-- Main content -->
    <main>
        <div class="container mb-5">
            <!-- Success message -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Details card -->
            <div class="detail-card card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-geo-alt-fill me-2"></i> {{ $collectionPoint->name }}
                    </h3>
                </div>
                <div class="info-section">

                    <div class="info-item">
                        <div class="info-icon"><i class="bi bi-geo-alt-fill"></i></div>
                        <div class="info-content">
                            <div class="info-label">Address</div>
                            <div class="info-value">{{ $collectionPoint->address }}, {{ $collectionPoint->city }} {{ $collectionPoint->postal_code }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="bi bi-telephone-fill"></i></div>
                        <div class="info-content">
                            <div class="info-label">Phone</div>
                            <div class="info-value">
                                @if ($collectionPoint->contact_phone)
                                    <a href="tel:{{ $collectionPoint->contact_phone }}" class="text-decoration-none">{{ $collectionPoint->contact_phone }}</a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="bi bi-info-circle-fill"></i></div>
                        <div class="info-content">
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                <span class="status-badge {{ $collectionPoint->status == 'active' ? 'status-active' : 'status-inactive' }}">
                                    <i class="fas fa-circle me-1" style="font-size: 0.6rem;"></i>
                                    {{ ucfirst($collectionPoint->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="bi bi-clock-fill"></i></div>
                        <div class="info-content">
                            <div class="info-label">Opening Hours</div>
                            <div class="info-value">
                                @if (is_array($collectionPoint->opening_hours) && count($collectionPoint->opening_hours) > 0)
                                    <ul class="hours-list">
                                        @foreach ($collectionPoint->opening_hours as $hour)
                                            <li>{{ $hour }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="bi bi-tags-fill"></i></div>
                        <div class="info-content">
                            <div class="info-label">Accepted Categories</div>
                            <div class="info-value">
                                @if (is_array($collectionPoint->accepted_categories) && count($collectionPoint->accepted_categories) > 0)
                                    @foreach ($collectionPoint->accepted_categories as $category)
                                        <span class="category-badge">{{ $category }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">No categories specified</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="bi bi-map-fill"></i></div>
                        <div class="info-content">
                            <div class="info-label">Location</div>
                            <div class="info-value">
                                <div class="map-container">
                                    <div id="map"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="last-updated">
                        <i class="bi bi-arrow-clockwise me-2 text-muted"></i>
                        <small class="text-muted">Last updated: {{ $collectionPoint->updated_at->format('d/m/Y at H:i') }}</small>
                    </div>

                    <div class="action-buttons">
                        <a href="{{ route('front.collectionpoints.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Back to list
                        </a>
                        @if ($collectionPoint->latitude && $collectionPoint->longitude)
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $collectionPoint->latitude }},{{ $collectionPoint->longitude }}" target="_blank" class="btn btn-success">
                                <i class="bi bi-signpost-split me-1"></i> Get Directions
                            </a>
                        @endif
                    </div>

                </div>

            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p class="mb-0">&copy; 2023 Biodex. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // Script for navbar that changes on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Map script
        document.addEventListener('DOMContentLoaded', function () {
            @if ($collectionPoint->latitude && $collectionPoint->longitude)
                var lat = {{ $collectionPoint->latitude }};
                var lon = {{ $collectionPoint->longitude }};
                initMap(lat, lon, "{{ $collectionPoint->name }}", "{{ $collectionPoint->address }}", "{{ $collectionPoint->city }} {{ $collectionPoint->postal_code }}");
            @else
                var address = "{{ $collectionPoint->address ?? '' }} {{ $collectionPoint->city ?? '' }} {{ $collectionPoint->postal_code ?? '' }} Tunisia";
                var url = `https://nominatim.openstreetmap.org/search?format=json&countrycodes=tn&limit=1&q=${encodeURIComponent(address)}`;

                fetch(url, { headers: { 'User-Agent': 'Biodex-App' }})
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            var lat = parseFloat(data[0].lat);
                            var lon = parseFloat(data[0].lon);
                            initMap(lat, lon, "{{ $collectionPoint->name }}", "{{ $collectionPoint->address }}", "{{ $collectionPoint->city }} {{ $collectionPoint->postal_code }}");
                        } else {
                            initMap(36.8065, 10.1815, "Tunis", "Tunisia", "");
                        }
                    })
                    .catch(error => {
                        console.error("Geocoding error:", error);
                        initMap(36.8065, 10.1815, "Tunis", "Tunisia", "");
                    });
            @endif
        });

        function initMap(lat, lon, name, address, city) {
            var map = L.map('map').setView([lat, lon], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            var greenIcon = L.divIcon({
                html: '<i class="bi bi-geo-alt-fill" style="font-size: 24px; color: #198754;"></i>',
                iconSize: [24, 24],
                className: 'leaflet-div-icon-custom'
            });

            L.marker([lat, lon], { icon: greenIcon })
                .addTo(map)
                .bindPopup(`
                    <div class="text-center">
                        <h5 class="mb-1">${name}</h5>
                        <p class="mb-1">${address}</p>
                        <p class="mb-0">${city}</p>
                    </div>
                `)
                .openPopup();
        }
    </script>
</body>
</html>