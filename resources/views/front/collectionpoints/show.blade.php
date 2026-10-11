{{-- resources/views/front/collectionpoints/show.blade.php --}}
@extends('front.layout')

@section('title', 'Collection Point Details')

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('hero')
    <section class="hero-header" style="height:50vh; margin-bottom:0;">
        <div class="hero-content text-start">
            <h1>Details of the <span>Collection Point</span></h1>
            <p class="lead mt-3">Complete information about {{ $collectionPoint->name }}</p>
        </div>
    </section>
@endsection

@section('content')
@php
    $isActive = $collectionPoint->status === 'active';
@endphp

<div class="cp-detail-page">
    <div class="container pb-4">

        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('front.collectionpoints.index') }}">Collection points</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $collectionPoint->name }}</li>
            </ol>
        </nav>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show soft-alert" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-12 col-lg-8">
                <div class="panel">
                    <div class="panel-head">
                        <h2><i class="fas fa-location-dot"></i> {{ $collectionPoint->name }}</h2>
                        <span class="status-pill {{ $isActive ? 'is-active' : 'is-inactive' }}">
                            {{ ucfirst($collectionPoint->status) }}
                        </span>
                    </div>

                    <div class="info-section">
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-map-pin"></i></div>
                            <div class="info-content">
                                <div class="info-label">Address</div>
                                <div class="info-value">{{ $collectionPoint->address }}, {{ $collectionPoint->city }} {{ $collectionPoint->postal_code }}</div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-phone"></i></div>
                            <div class="info-content">
                                <div class="info-label">Phone</div>
                                <div class="info-value">
                                    @if ($collectionPoint->contact_phone)
                                        <a href="tel:{{ $collectionPoint->contact_phone }}" class="link-brand">{{ $collectionPoint->contact_phone }}</a>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-clock"></i></div>
                            <div class="info-content">
                                <div class="info-label">Opening hours</div>
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
                          
                            
                        </div>
                    </div>
                </div>

                <div class="panel mt-4">
                    <div class="panel-head">
                        <h2><i class="fas fa-map"></i> Location</h2>
                    </div>
                    <div class="map-frame">
                        <div id="map"></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="panel sticky-actions">
                    <div class="panel-head">
                        <h2><i class="fas fa-circle-info"></i> Actions</h2>
                    </div>
                    <div class="panel-body">
                        <div class="side-status health-row">
                            <span>Status</span>
                            <span class="status-pill {{ $isActive ? 'is-active' : 'is-inactive' }}">{{ ucfirst($collectionPoint->status) }}</span>
                        </div>

                        <div class="side-actions">
                            @if ($collectionPoint->latitude && $collectionPoint->longitude)
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $collectionPoint->latitude }},{{ $collectionPoint->longitude }}"
                                   target="_blank" rel="noopener" class="btn btn-brand w-100">
                                    <i class="fas fa-diamond-turn-right me-2"></i>Get directions
                                </a>
                            @endif
                            <a href="{{ route('front.collectionpoints.index') }}" class="btn btn-brand-outline w-100">
                                <i class="fas fa-arrow-left me-2"></i>Back to list
                            </a>
                        </div>

                        <div class="last-updated">
                            <i class="fas fa-rotate me-2"></i>
                            <small>Last updated: {{ optional($collectionPoint->updated_at)->format('d/m/Y \a\t H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if ($collectionPoint->latitude && $collectionPoint->longitude)
            initMap({{ $collectionPoint->latitude }}, {{ $collectionPoint->longitude }},
                @json($collectionPoint->name), @json($collectionPoint->address),
                @json(trim($collectionPoint->city . ' ' . $collectionPoint->postal_code)));
        @else
            var address = @json(trim(($collectionPoint->address ?? '') . ' ' . ($collectionPoint->city ?? '') . ' ' . ($collectionPoint->postal_code ?? '') . ' Tunisia'));
            var url = `https://nominatim.openstreetmap.org/search?format=json&countrycodes=tn&limit=1&q=${encodeURIComponent(address)}`;

            fetch(url, { headers: { 'User-Agent': 'Biodex-App' }})
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        initMap(parseFloat(data[0].lat), parseFloat(data[0].lon),
                            @json($collectionPoint->name), @json($collectionPoint->address),
                            @json(trim($collectionPoint->city . ' ' . $collectionPoint->postal_code)));
                    } else {
                        initMap(36.8065, 10.1815, 'Tunis', 'Tunisia', '');
                    }
                })
                .catch(error => {
                    console.error("Geocoding error:", error);
                    initMap(36.8065, 10.1815, 'Tunis', 'Tunisia', '');
                });
        @endif
    });

    function initMap(lat, lon, name, address, city) {
        var map = L.map('map').setView([lat, lon], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        var greenIcon = L.divIcon({
            html: '<i class="fas fa-location-dot" style="font-size:26px; color:#047857;"></i>',
            iconSize: [26, 26],
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

<style>
    .cp-detail-page {
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
    .cp-detail-page .breadcrumb { --bs-breadcrumb-divider-color: #aab5af; }
    .cp-detail-page .breadcrumb a { color: var(--green-700); text-decoration: none; }
    .cp-detail-page .breadcrumb a:hover { text-decoration: underline; }
    .link-brand { color: var(--green-700); text-decoration: none; font-weight: 600; }
    .link-brand:hover { text-decoration: underline; }

    .panel {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(20, 35, 28, .05);
    }
    .panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--line);
        background: var(--mint);
    }
    .panel-head h2 {
        font-size: 1.02rem;
        font-weight: 700;
        margin: 0;
        color: var(--green-700);
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .panel-body { padding: 1.25rem; }

    .info-section { padding: .5rem 1.25rem 1.25rem; }
    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.1rem 0;
        border-bottom: 1px solid var(--line);
    }
    .info-item:last-child { border-bottom: 0; padding-bottom: .25rem; }
    .info-icon {
        flex-shrink: 0;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--mint);
        color: var(--green-700);
    }
    .info-content { flex: 1; }
    .info-label { font-weight: 700; font-size: .82rem; text-transform: uppercase; letter-spacing: .04em; color: var(--muted); margin-bottom: .35rem; }
    .info-value { color: var(--ink); }

    .hours-list { list-style: none; padding-left: 0; margin-bottom: 0; }
    .hours-list li { padding: .3rem 0; border-bottom: 1px dashed var(--line); }
    .hours-list li:last-child { border-bottom: none; }

    .chip-row { display: flex; flex-wrap: wrap; gap: .4rem; }
    .chip {
        display: inline-block;
        background: var(--mint);
        color: var(--green-700);
        padding: .35rem .8rem;
        border-radius: 999px;
        font-size: .82rem;
        font-weight: 600;
        border: 1px solid #c7eeda;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .3rem .75rem;
        border-radius: 999px;
        font-size: .75rem;
        font-weight: 700;
        white-space: nowrap;
        background: #eef1ef;
        color: #44554c;
    }
    .status-pill::before { content: ""; width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
    .status-pill.is-active   { background: #dcfce7; color: #065f46; }
    .status-pill.is-inactive { background: #fee2e2; color: #991b1b; }

    .map-frame { padding: 0; }
    #map { height: 320px; width: 100%; background: #e8ecea; }
    .leaflet-div-icon-custom { background: transparent; border: none; }

    .sticky-actions { position: sticky; top: 90px; }
    .health-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid var(--line);
        font-size: .9rem;
        color: var(--muted);
    }
    .side-actions { display: flex; flex-direction: column; gap: .6rem; }
    .last-updated {
        margin-top: 1rem;
        padding: .8rem;
        border-radius: 10px;
        background: rgba(0, 0, 0, .03);
        text-align: center;
        color: var(--muted);
        font-size: .82rem;
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
        padding: .65rem 1.25rem;
        border-radius: 10px;
        transition: background .2s ease, color .2s ease;
    }
    .btn-brand-outline:hover, .btn-brand-outline:focus-visible { background: var(--green-700); color: #fff; }
    .soft-alert { border: 0; border-radius: 12px; background: var(--mint); color: var(--green-700); }

    @media (max-width: 991.98px) {
        .sticky-actions { position: static; }
    }
</style>
@endsection
