@extends('back.layout')

@section('title', 'Collection Points')

@section('content')
<style>
/* ===== Statistics (uniformisé avec les autres modules) ===== */
.collection-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stats-wrapper {
    background: linear-gradient(135deg, #f2dd94, #e8c471);
    border-radius: 25px;
    padding: 2rem;
    color: #1b4332;
    margin-bottom: 2.5rem;
}

.stats-wrapper .stat-card {
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    padding: 1.8rem;
    backdrop-filter: blur(12px);
    box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
    color: #fff;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.stats-wrapper .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.stats-wrapper .stat-card::before {
    content: "";
    position: absolute;
    top: -40%;
    right: -40%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle at top right, rgba(255,255,255,0.2), transparent 70%);
    transform: rotate(25deg);
    pointer-events: none;
}

.stats-wrapper .stat-icon {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    opacity: 0.9;
    color: #1b4332;
}

.stats-wrapper .stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #1b4332;
}

.stats-wrapper .stat-label {
    opacity: 0.9;
    font-size: 0.95rem;
    letter-spacing: 0.5px;
    color: #1b4332;
}

/* ===== Table Card ===== */
.table-card {
    background: #ffffff;
    border-radius: 15px;
    box-shadow: 0 6px 30px rgba(0,0,0,0.08);
    overflow: hidden;
    border: none;
}
.table thead th { text-align: center; padding: 1rem; vertical-align: middle; white-space: nowrap; }
.table tbody tr { transition: all 0.25s ease; }
.table tbody tr:hover { background-color: rgba(25, 135, 84, 0.05); }
.table td { vertical-align: middle; text-align: center; padding: 0.9rem; }

.table thead th a.sort-link {
    color: #198754;
    text-decoration: none;
    font-weight: 600;
    white-space: nowrap;
}
.table thead th a.sort-link:hover { color: #0f5132; text-decoration: underline; }

/* ===== Filter Bar ===== */
.filter-bar {
    background: #ffffff;
    border-radius: 15px;
    box-shadow: 0 6px 30px rgba(0,0,0,0.06);
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
}
.filter-bar .form-control,
.filter-bar .form-select { border-radius: 10px; }

/* ===== Action Buttons ===== */
.action-buttons { display: flex; gap: 0.5rem; justify-content: center; align-items: center; }

.action-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 2.5rem !important;
    height: 2.5rem !important;
    padding: 0 !important;
    border-radius: 8px !important;
    border: none !important;
    text-decoration: none !important;
    transition: all 0.3s ease;
    color: #fff !important;
    cursor: pointer !important;
    position: relative;
    overflow: hidden;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* === FIX: icônes toujours visibles === */
.action-btn > i,
.action-btn > .bi,
.action-btn i::before {
    display: inline-block !important;
    font-family: "bootstrap-icons" !important;
    font-size: 1rem !important;
    line-height: 1 !important;
    color: #fff !important;
    opacity: 1 !important;
    visibility: visible !important;
    position: relative !important;
    z-index: 3 !important;
    margin: 0 !important;
    transition: transform 0.2s ease;
}

.action-btn:hover > i { transform: scale(1.1); }

.action-view   { background: linear-gradient(135deg,#0dcaf0,#0aa2c0) !important; }
.action-view:hover { background: linear-gradient(135deg,#0aa2c0,#087990) !important; }
.action-edit   { background: linear-gradient(135deg,#ffc107,#e0a800) !important; }
.action-edit:hover { background: linear-gradient(135deg,#e0a800,#c69500) !important; }
.action-delete { background: linear-gradient(135deg,#dc3545,#c82333) !important; }
.action-delete:hover { background: linear-gradient(135deg,#c82333,#a71e2a) !important; }

/* Le formulaire de suppression ne doit pas casser la mise en page */
form.d-inline { display: inline-flex; margin: 0; padding: 0; }

/* ===== Page Header ===== */
.page-header {
    display: flex; flex-wrap: wrap; justify-content: space-between;
    align-items: center; margin-bottom: 1.5rem; gap: 1rem;
}
.page-header h2 { font-weight: 700; color: #0d6efd; }

.btn-add {
    background: linear-gradient(135deg, #00c9a7, #007bff);
    border: none; color: white; border-radius: 30px;
    padding: 0.7rem 1.8rem; font-weight: 600;
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
}
.btn-add:hover {
    background: linear-gradient(135deg, #007bff, #00c9a7);
    transform: translateY(-2px); color: #fff;
}

/* ===== Pagination ===== */
.pagination { margin-bottom: 0; }
.pagination .page-link { color: #198754; }
.pagination .page-item.active .page-link {
    background-color: #198754; border-color: #198754; color: #fff;
}

/* ===== Fade in ===== */
.fade-in-up { animation: fadeInUp 0.6s ease-out; }
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(25px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ===== Responsive ===== */
@media (max-width: 992px) {
    .table-card { overflow-x: auto; }
    #collectionpoints-table { min-width: 1100px; }
}

@media (max-width: 768px) {
    .stats-wrapper { padding: 1rem; }
    .btn-add { width: 100%; margin-top: 1rem; justify-content: center; }
    .page-header .d-flex.flex-wrap.gap-2 { width: 100%; }
    .page-header .d-flex.flex-wrap.gap-2 > * { flex: 1 1 auto; }
}
</style>

@php
    $queryParams = request()->query();
    $sortLink = function ($column) use ($queryParams) {
        $isCurrent = request('sort') === $column;
        $direction = ($isCurrent && request('direction') !== 'desc') ? 'desc' : 'asc';
        $params = array_merge($queryParams, ['sort' => $column, 'direction' => $direction]);
        return request()->url() . '?' . http_build_query($params);
    };
    $sortIcon = function ($column) {
        if (request('sort') !== $column) {
            return '<i class="bi bi-arrow-down-up small opacity-50" aria-hidden="true"></i>';
        }
        return request('direction') === 'desc'
            ? '<i class="bi bi-sort-down" aria-hidden="true"></i>'
            : '<i class="bi bi-sort-up" aria-hidden="true"></i>';
    };
    $exportParams = array_filter(
        request()->only('search', 'status', 'city'),
        fn ($value) => $value !== null && $value !== ''
    );
@endphp

<div class="container-fluid py-4 fade-in-up">

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('import_errors') && count(session('import_errors')))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong><i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>Lignes ignorées :</strong>
            <ul class="mb-0 mt-2">
                @foreach (session('import_errors') as $importError)
                    <li>{{ $importError }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Statistics (uniformisé) --}}
    <div class="stats-wrapper fade-in-up">
        <div class="collection-stats">
            <div class="stat-card">
                <i class="bi bi-recycle stat-icon" aria-hidden="true"></i>
                <div class="stat-number">{{ number_format((int) ($total ?? 0)) }}</div>
                <div class="stat-label">Total Collection Points</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-check-circle-fill stat-icon" aria-hidden="true"></i>
                <div class="stat-number">{{ number_format((int) ($activeCount ?? 0)) }}</div>
                <div class="stat-label">Active Points</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-pause-circle stat-icon" aria-hidden="true"></i>
                <div class="stat-number">{{ number_format((int) ($inactiveCount ?? 0)) }}</div>
                <div class="stat-label">Inactive Points</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-geo-alt stat-icon" aria-hidden="true"></i>
                <div class="stat-number">{{ number_format(isset($cities) ? $cities->count() : 0) }}</div>
                <div class="stat-label">Cities Covered</div>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <div class="page-header">
        <div>
            <h2 class="fw-bold text-success mb-1">
                <i class="bi bi-recycle me-2" aria-hidden="true"></i>Collection Points Management
            </h2>
            <p class="text-muted mb-0">Manage waste collection points across cities</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('collectionpoints.export.csv', $exportParams) }}" class="btn btn-outline-success">
                <i class="bi bi-filetype-csv me-1" aria-hidden="true"></i>Export CSV
            </a>
            <a href="{{ route('collectionpoints.export.pdf', $exportParams) }}" class="btn btn-outline-danger">
                <i class="bi bi-filetype-pdf me-1" aria-hidden="true"></i>Export PDF
            </a>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-upload me-1" aria-hidden="true"></i>Import CSV
            </button>
            <a href="{{ route('collectionpoints.create') }}" class="btn btn-add">
                <i class="bi bi-plus-circle-fill me-2" aria-hidden="true"></i>New Collection Point
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('collectionpoints.index') }}" class="filter-bar">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label mb-1 small text-muted">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control" placeholder="Name, address, city, phone...">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1 small text-muted">City</label>
                <select name="city" class="form-select">
                    <option value="">All cities</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city }}" @selected(request('city') === $city)>{{ $city }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1 small text-muted">Status</label>
                <select name="status" class="form-select">
                    <option value="">All statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1 small text-muted">Per page</label>
                <select name="per_page" class="form-select">
                    @foreach ([5, 10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" @selected((int) request('per_page', 10) === $size)>{{ $size }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-success flex-fill">
                    <i class="bi bi-funnel-fill me-1" aria-hidden="true"></i>Filter
                </button>
                <a href="{{ route('collectionpoints.index') }}"
                   class="btn btn-outline-secondary"
                   title="Reset filters"
                   aria-label="Reset filters">
                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </form>

    @if ($collectionPoints->isEmpty())
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox display-4 text-muted mb-3" aria-hidden="true"></i>
                <h4 class="text-muted mb-3">No Collection Points Found</h4>
                <p class="text-muted mb-4">
                    {{ request('search') || request('status') || request('city')
                        ? 'Try changing or clearing your filters.'
                        : 'Start by adding your first collection point' }}
                </p>
                @if(!request('search') && !request('status') && !request('city'))
                    <a href="{{ route('collectionpoints.create') }}" class="btn btn-success px-4">
                        <i class="bi bi-plus-circle me-2" aria-hidden="true"></i> Create Collection Point
                    </a>
                @else
                    <a href="{{ route('collectionpoints.index') }}" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-x-circle me-2" aria-hidden="true"></i> Clear Filters
                    </a>
                @endif
            </div>
        </div>
    @else
        {{-- Table --}}
        <div class="table-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="collectionpoints-table" class="table table-hover align-middle w-100 mb-0">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th><a class="sort-link" href="{{ $sortLink('id') }}">ID {!! $sortIcon('id') !!}</a></th>
                                <th><a class="sort-link" href="{{ $sortLink('name') }}">Name {!! $sortIcon('name') !!}</a></th>
                                <th><a class="sort-link" href="{{ $sortLink('address') }}">Address {!! $sortIcon('address') !!}</a></th>
                                <th><a class="sort-link" href="{{ $sortLink('city') }}">City {!! $sortIcon('city') !!}</a></th>
                                <th><a class="sort-link" href="{{ $sortLink('contact_phone') }}">Contact {!! $sortIcon('contact_phone') !!}</a></th>
                                <th><a class="sort-link" href="{{ $sortLink('status') }}">Status {!! $sortIcon('status') !!}</a></th>
                                <th><i class="bi bi-gear" aria-hidden="true"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($collectionPoints as $collectionPoint)
                                <tr class="text-center">
                                    <td>{{ $collectionPoint->id }}</td>
                                    <td><strong>{{ $collectionPoint->name }}</strong></td>
                                    <td>
                                        <span class="d-inline-block text-wrap"
                                              style="max-width: 220px; white-space: normal; word-break: break-word;">
                                            {{ $collectionPoint->address }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $collectionPoint->city }}</span>
                                    </td>
                                    <td>
                                        @if($collectionPoint->contact_phone)
                                            <a href="tel:{{ $collectionPoint->contact_phone }}" class="text-decoration-none text-dark">
                                                <i class="bi bi-telephone me-1" aria-hidden="true"></i>{{ $collectionPoint->contact_phone }}
                                            </a>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-25 text-secondary">Undefined</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php $status = $collectionPoint->status ?? 'undefined'; @endphp
                                        @if ($status === 'active')
                                            <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25 d-inline-flex align-items-center justify-content-center">
                                                <i class="bi bi-check-circle me-1 small" aria-hidden="true"></i> Active
                                            </span>
                                        @elseif ($status === 'inactive')
                                            <span class="badge bg-secondary bg-opacity-15 text-secondary border border-secondary border-opacity-25 d-inline-flex align-items-center justify-content-center">
                                                <i class="bi bi-x-circle me-1 small" aria-hidden="true"></i> Inactive
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-15 text-warning border border-warning border-opacity-25 d-inline-flex align-items-center justify-content-center">
                                                <i class="bi bi-question-circle me-1 small" aria-hidden="true"></i> Undefined
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            {{-- Bouton Voir / Détails --}}
                                            <a href="{{ route('collectionpoints.show', $collectionPoint->id) }}"
                                               class="action-btn action-view"
                                               data-bs-toggle="tooltip"
                                               aria-label="View collection point"
                                               title="View Details">
                                                <i class="bi bi-eye-fill" aria-hidden="true"></i>
                                            </a>

                                            {{-- Bouton Modifier --}}
                                            <a href="{{ route('collectionpoints.edit', $collectionPoint->id) }}"
                                               class="action-btn action-edit"
                                               data-bs-toggle="tooltip"
                                               aria-label="Edit collection point"
                                               title="Edit Collection Point">
                                                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                            </a>

                                            {{-- Bouton Supprimer --}}
                                            <form action="{{ route('collectionpoints.destroy', $collectionPoint->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this collection point?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="action-btn action-delete"
                                                        data-bs-toggle="tooltip"
                                                        aria-label="Delete collection point"
                                                        title="Delete Collection Point">
                                                    <i class="bi bi-trash3-fill" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 gap-2">
            <div class="text-muted small">
                Showing {{ $collectionPoints->firstItem() }}–{{ $collectionPoints->lastItem() }}
                of {{ $collectionPoints->total() }} entries
            </div>
            {{ $collectionPoints->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

{{-- Import Modal --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('collectionpoints.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="bi bi-upload me-2" aria-hidden="true"></i>Import Collection Points
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">CSV file</label>
                        <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv" required>
                        @error('csv_file')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-upload me-1" aria-hidden="true"></i>Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Bootstrap JS est supposé déjà chargé par back.layout.
     Si ce n'est PAS le cas, décommenter la ligne ci-dessous. --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> --}}

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Tooltips délégués : fonctionne même pour les éléments ajoutés dynamiquement
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            new bootstrap.Tooltip(document.body, {
                selector: '[data-bs-toggle="tooltip"]',
                trigger: 'hover'
            });
        }

        @if ($errors->has('csv_file'))
            var importModalEl = document.getElementById('importModal');
            if (importModalEl && typeof bootstrap !== 'undefined') {
                new bootstrap.Modal(importModalEl).show();
            }
        @endif
    });
</script>
@endsection