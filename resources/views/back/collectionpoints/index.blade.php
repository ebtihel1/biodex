@extends('back.layout')

@section('title', 'Collection Points')

@section('content')
<style>
/* Statistics Section */
.collection-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
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

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.stat-card::before {
    content: "";
    position: absolute;
    top: -40%;
    right: -40%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle at top right, rgba(255,255,255,0.2), transparent 70%);
    transform: rotate(25deg);
}

.stat-icon {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    opacity: 0.9;
    color: #1b4332;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #1b4332;
}

.stat-label {
    opacity: 0.9;
    font-size: 0.95rem;
    letter-spacing: 0.5px;
    color: #1b4332;
}

.stats-wrapper {
    background: linear-gradient(135deg, #f2dd94, #e8c471);
    border-radius: 25px;
    padding: 2rem;
    color: #1b4332;
    margin-bottom: 2.5rem;
}

.table-card {
    background: #ffffff;
    border-radius: 15px;
    box-shadow: 0 6px 30px rgba(0,0,0,0.08);
    overflow: hidden;
    border: none;
}

.table thead th {
    text-align: center;
    padding: 1rem;
    vertical-align: middle;
}

.table tbody tr {
    transition: all 0.25s ease;
}

.table tbody tr:hover {
    background-color: rgba(25, 135, 84, 0.05);
}

.table td {
    vertical-align: middle;
    text-align: center;
    padding: 0.9rem;
}

/* Sortable headers */
.table thead th a.sort-link {
    color: #198754;
    text-decoration: none;
    font-weight: 600;
    white-space: nowrap;
}

.table thead th a.sort-link:hover {
    color: #0f5132;
    text-decoration: underline;
}

/* Filter bar */
.filter-bar {
    background: #ffffff;
    border-radius: 15px;
    box-shadow: 0 6px 30px rgba(0,0,0,0.06);
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
}

.filter-bar .form-control,
.filter-bar .form-select {
    border-radius: 10px;
}

/* Action Buttons */
.btn-action {
    border: none;
    border-radius: 10px;
    padding: 0.4rem 0.7rem;
    transition: all 0.3s ease;
}

.btn-action:hover {
    transform: scale(1.1);
}

/* Filters and Header */
.page-header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.page-header h2 {
    font-weight: 700;
    color: #0d6efd;
}

.btn-add {
    background: linear-gradient(135deg, #00c9a7, #007bff);
    border: none;
    color: white;
    border-radius: 30px;
    padding: 0.7rem 1.8rem;
    font-weight: 600;
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
}

.btn-add:hover {
    background: linear-gradient(135deg, #007bff, #00c9a7);
    transform: translateY(-2px);
}

/* Smooth Animation */
.fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(25px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .stats-wrapper {
        padding: 1rem;
    }
    .btn-add {
        width: 100%;
        margin-top: 1rem;
    }
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    align-items: center;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 8px;
    border: none;
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    color: #fff !important;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.action-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.action-btn:hover::before {
    left: 100%;
}

.action-btn i {
    font-size: 1rem;
    transition: transform 0.2s ease;
}

.action-btn:hover i {
    transform: scale(1.1);
}

.action-edit {
    background: linear-gradient(135deg, #ffc107, #e0a800);
}
.action-edit:hover {
    background: linear-gradient(135deg, #e0a800, #c69500);
}

.action-delete {
    background: linear-gradient(135deg, #dc3545, #c82333);
}
.action-delete:hover {
    background: linear-gradient(135deg, #c82333, #a71e2a);
}

/* Pagination tuning (Bootstrap 5) */
.pagination {
    margin-bottom: 0;
}
.pagination .page-link {
    color: #198754;
}
.pagination .page-item.active .page-link {
    background-color: #198754;
    border-color: #198754;
    color: #fff;
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
            return '<i class="bi bi-arrow-down-up small opacity-50"></i>';
        }
        return request('direction') === 'desc'
            ? '<i class="bi bi-sort-down"></i>'
            : '<i class="bi bi-sort-up"></i>';
    };
    $exportParams = array_filter(
        request()->only('search', 'status', 'city'),
        fn ($value) => $value !== null && $value !== ''
    );
@endphp

<div class="container-fluid py-4 fade-in-up">
    <!-- Statistics -->
    <div class="stats-wrapper fade-in-up">
        <div class="collection-stats">
            <div class="stat-card bg-gradient-primary">
                <i class="bi bi-recycle stat-icon"></i>
                <div class="stat-number">{{ $total }}</div>
                <div class="stat-label">Total Collection Points</div>
            </div>
            <div class="stat-card bg-gradient-success">
                <i class="bi bi-check-circle-fill stat-icon"></i>
                <div class="stat-number">{{ $activeCount }}</div>
                <div class="stat-label">Active Points</div>
            </div>
            <div class="stat-card bg-gradient-warning">
                <i class="bi bi-pause-circle stat-icon"></i>
                <div class="stat-number">{{ $inactiveCount }}</div>
                <div class="stat-label">Inactive Points</div>
            </div>
            <div class="stat-card bg-gradient-secondary">
                <i class="bi bi-geo-alt stat-icon"></i>
                <div class="stat-number">{{ $cities->count() }}</div>
                <div class="stat-label">Cities Covered</div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div class="page-header">
        <h2 class="fw-bold text-success mb-0">
            <i class="bi bi-recycle me-2"></i>Collection Points Management
        </h2>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('collectionpoints.export.csv', $exportParams) }}" class="btn btn-outline-success">
                <i class="bi bi-filetype-csv me-1"></i>Export CSV
            </a>
            <a href="{{ route('collectionpoints.export.pdf', $exportParams) }}" class="btn btn-outline-danger">
                <i class="bi bi-filetype-pdf me-1"></i>Export PDF
            </a>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-upload me-1"></i>Import CSV
            </button>
            <a href="{{ route('collectionpoints.create') }}" class="btn btn-add">
                <i class="bi bi-plus-circle-fill me-2"></i>New Collection Point
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('import_errors') && count(session('import_errors')))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Lignes ignorées :</strong>
            <ul class="mb-0 mt-2">
                @foreach (session('import_errors') as $importError)
                    <li>{{ $importError }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filters -->
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
                    <i class="bi bi-funnel-fill me-1"></i>Filter
                </button>
                <a href="{{ route('collectionpoints.index') }}" class="btn btn-outline-secondary" title="Reset filters">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </div>
    </form>

    @if ($collectionPoints->isEmpty())
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                <h4 class="text-muted mb-3">No Collection Points Found</h4>
                <p class="text-muted mb-4">Start by adding your first collection point</p>
                <a href="{{ route('collectionpoints.create') }}" class="btn btn-success px-4">
                    <i class="bi bi-plus-circle me-2"></i> Create Collection Point
                </a>
            </div>
        </div>
    @else
        <!-- Table -->
        <div class="table-card">
            <div class="card-body p-0">
                <table id="collectionpoints-table" class="table table-hover align-middle w-100 mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th><a class="sort-link" href="{{ $sortLink('id') }}">ID {!! $sortIcon('id') !!}</a></th>
                            <th><a class="sort-link" href="{{ $sortLink('name') }}">Name {!! $sortIcon('name') !!}</a></th>
                            <th><a class="sort-link" href="{{ $sortLink('address') }}">Address {!! $sortIcon('address') !!}</a></th>
                            <th><a class="sort-link" href="{{ $sortLink('city') }}">City {!! $sortIcon('city') !!}</a></th>
                            <th><a class="sort-link" href="{{ $sortLink('postal_code') }}">Postal Code {!! $sortIcon('postal_code') !!}</a></th>
                            <th><i class="bi bi-clock"></i> Opening Hours</th>
                            <th><i class="bi bi-tags"></i> Categories</th>
                            <th><a class="sort-link" href="{{ $sortLink('contact_phone') }}">Contact {!! $sortIcon('contact_phone') !!}</a></th>
                            <th><a class="sort-link" href="{{ $sortLink('status') }}">Status {!! $sortIcon('status') !!}</a></th>
                            <th><i class="bi bi-gear"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($collectionPoints as $collectionPoint)
                            <tr class="text-center">
                                <td>{{ $collectionPoint->id }}</td>
                                <td>
                                    <strong>{{ $collectionPoint->name }}</strong>
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 200px;">
                                        {{ $collectionPoint->address }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $collectionPoint->city }}</span>
                                </td>
                                <td>
                                    <code class="text-dark">{{ $collectionPoint->postal_code }}</code>
                                </td>
                                <td>
                                    @php
                                        $openingHours = $collectionPoint->opening_hours;
                                        if (is_string($openingHours)) {
                                            $openingHours = json_decode($openingHours, true);
                                        }
                                    @endphp

                                    @if (is_array($openingHours) && !empty($openingHours))
                                        <button class="btn btn-sm btn-outline-info border-0"
                                                data-bs-toggle="tooltip"
                                                data-bs-html="true"
                                                title="<strong>Opening Hours:</strong><br>@foreach($openingHours as $horaire)• {{ $horaire }}<br>@endforeach">
                                            <i class="bi bi-clock"></i>
                                        </button>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-25 text-secondary">Undefined</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $categories = $collectionPoint->accepted_categories;
                                        if (is_string($categories)) {
                                            $categories = json_decode($categories, true);
                                        }
                                    @endphp

                                    @if (is_array($categories) && !empty($categories))
                                        <div class="d-flex flex-wrap gap-1 justify-content-center" style="max-width: 150px;">
                                            @foreach(array_slice($categories, 0, 2) as $category)
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 small">
                                                    {{ $category }}
                                                </span>
                                            @endforeach
                                            @if(count($categories) > 2)
                                                <span class="badge bg-light text-muted border small" data-bs-toggle="tooltip"
                                                      title="{{ implode(', ', array_slice($categories, 2)) }}">
                                                    +{{ count($categories) - 2 }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-25 text-secondary">Undefined</span>
                                    @endif
                                </td>
                                <td>
                                    @if($collectionPoint->contact_phone)
                                        <div class="d-flex flex-column">
                                            <a href="tel:{{ $collectionPoint->contact_phone }}" class="text-decoration-none text-dark">
                                                <i class="bi bi-telephone me-1"></i>{{ $collectionPoint->contact_phone }}
                                            </a>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-25 text-secondary">Undefined</span>
                                    @endif
                                </td>
                                <td>
                                    @php $status = $collectionPoint->status ?? 'undefined'; @endphp
                                    @if ($status === 'active')
                                        <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25 d-flex align-items-center justify-content-center" style="width: fit-content;">
                                            <i class="bi bi-check-circle me-1 small"></i> Active
                                        </span>
                                    @elseif ($status === 'inactive')
                                        <span class="badge bg-secondary bg-opacity-15 text-secondary border border-secondary border-opacity-25 d-flex align-items-center justify-content-center" style="width: fit-content;">
                                            <i class="bi bi-x-circle me-1 small"></i> Inactive
                                        </span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-15 text-warning border border-warning border-opacity-25 d-flex align-items-center justify-content-center" style="width: fit-content;">
                                            <i class="bi bi-question-circle me-1 small"></i> Undefined
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('collectionpoints.edit', $collectionPoint->id) }}"
                                           class="action-btn action-edit"
                                           data-bs-toggle="tooltip"
                                           title="Edit Collection Point">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ route('collectionpoints.destroy', $collectionPoint->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="action-btn action-delete"
                                                    onclick="return confirm('Are you sure you want to delete this collection point?')"
                                                    data-bs-toggle="tooltip"
                                                    title="Delete Collection Point">
                                                <i class="bi bi-trash3"></i>
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

        <!-- Pagination -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 gap-2">
            <div class="text-muted small">
                Showing {{ $collectionPoints->firstItem() }}–{{ $collectionPoints->lastItem() }}
                of {{ $collectionPoints->total() }} entries
            </div>
            {{ $collectionPoints->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('collectionpoints.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel"><i class="bi bi-upload me-2"></i>Import Collection Points</h5>
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
                    <p class="text-muted small mb-0">
                        The first line may contain headers (name, address, city, postal_code, latitude,
                        longitude, contact_phone, status, opening_hours, accepted_categories).
                        Use the CSV export as a template. Separator <code>;</code> or <code>,</code> is detected automatically.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-upload me-1"></i>Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        @if ($errors->has('csv_file'))
            new bootstrap.Modal(document.getElementById('importModal')).show();
        @endif
    });
</script>
@endsection
