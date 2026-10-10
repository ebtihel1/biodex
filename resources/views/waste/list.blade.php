@extends('back.layout')

@section('title', 'Waste List')

@section('content')
<style>
/* Statistics Section */
.campaign-stats {
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

.bg-gradient-primary,
.bg-gradient-success,
.bg-gradient-warning {
    background: linear-gradient(135deg, #f8e3a3ff, #edd58bff) !important;
}

/* Table card */
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

.table tbody tr { transition: all 0.25s ease; }
.table tbody tr:hover { background-color: rgba(25, 135, 84, 0.05); }

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
.filter-bar .form-select { border-radius: 10px; }

/* Action buttons */
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
    color: #fff !important;
}
.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.action-view   { background: linear-gradient(135deg, #0dcaf0, #0aa2c0); }
.action-view:hover { background: linear-gradient(135deg, #0aa2c0, #087990); }
.action-edit   { background: linear-gradient(135deg, #ffc107, #e0a800); }
.action-edit:hover { background: linear-gradient(135deg, #e0a800, #c69500); }
.action-delete { background: linear-gradient(135deg, #dc3545, #c82333); }
.action-delete:hover { background: linear-gradient(135deg, #c82333, #a71e2a); }

/* Header */
.page-header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
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
    color: #fff;
}

/* Pagination */
.pagination { margin-bottom: 0; }
.pagination .page-link { color: #198754; }
.pagination .page-item.active .page-link {
    background-color: #198754;
    border-color: #198754;
    color: #fff;
}

/* Fade in */
.fade-in-up { animation: fadeInUp 0.6s ease-out; }
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(25px); }
    to   { opacity: 1; transform: translateY(0); }
}

@media (max-width: 768px) {
    .stats-wrapper { padding: 1rem; }
    .btn-add { width: 100%; margin-top: 1rem; }
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
        request()->only('search', 'status', 'category'),
        fn ($value) => $value !== null && $value !== ''
    );
@endphp

<div class="container-fluid py-4 fade-in-up">

    {{-- Statistics --}}
    <div class="stats-wrapper fade-in-up">
        <div class="campaign-stats">
            <div class="stat-card">
                <i class="bi bi-box-seam stat-icon"></i>
                <div class="stat-number">{{ number_format((int) $summary->total_count) }}</div>
                <div class="stat-label">Total Items</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-speedometer2 stat-icon"></i>
                <div class="stat-number">{{ number_format((float) $summary->total_weight, 2) }} kg</div>
                <div class="stat-label">Total Recorded Weight</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-arrow-repeat stat-icon"></i>
                <div class="stat-number">
                    {{ number_format((int) $summary->recyclable_count) }}
                    <span class="fw-normal">/</span>
                    {{ number_format((int) $summary->reusable_count) }}
                </div>
                <div class="stat-label">Recyclable / Reusable</div>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <div class="page-header">
        <h2 class="fw-bold text-success mb-0">
            <i class="bi bi-recycle me-2"></i>Waste Inventory
        </h2>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('wastes.export.csv', $exportParams) }}" class="btn btn-outline-success">
                <i class="bi bi-filetype-csv me-1"></i>Export CSV
            </a>
            <a href="{{ route('wastes.export.pdf', $exportParams) }}" class="btn btn-outline-danger">
                <i class="bi bi-filetype-pdf me-1"></i>Export PDF
            </a>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-upload me-1"></i>Import CSV
            </button>
            <a href="{{ route('wastes.create') }}" class="btn btn-add">
                <i class="bi bi-plus-circle-fill me-2"></i>Add Waste
            </a>
        </div>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
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

    {{-- Filters --}}
    <form method="GET" action="{{ route('wastes.index') }}" class="filter-bar">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label mb-1 small text-muted">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control" placeholder="Type, description, category, user, point...">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1 small text-muted">Status</label>
                <select name="status" class="form-select">
                    <option value="">All statuses</option>
                    <option value="recyclable" @selected(request('status') === 'recyclable')>Recyclable</option>
                    <option value="reusable"   @selected(request('status') === 'reusable')>Reusable</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1 small text-muted">Category</label>
                <select name="category" class="form-select">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1 small text-muted">Per page</label>
                <select name="per_page" class="form-select">
                    @foreach ([5, 12, 25, 50, 100] as $size)
                        <option value="{{ $size }}" @selected((int) request('per_page', 12) === $size)>{{ $size }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-success flex-fill">
                    <i class="bi bi-funnel-fill me-1"></i>Filter
                </button>
                <a href="{{ route('wastes.index') }}" class="btn btn-outline-secondary" title="Reset filters">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </div>
    </form>

    @if ($wastes->isEmpty())
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                <h4 class="text-muted mb-3">No waste items found</h4>
                <p class="text-muted mb-4">
                    {{ request('search') || request('status') || request('category')
                        ? 'Try changing or clearing your filters.'
                        : 'Add a waste record to start building the inventory.' }}
                </p>
                @if(!request('search') && !request('status') && !request('category'))
                    <a href="{{ route('wastes.create') }}" class="btn btn-success px-4">
                        <i class="bi bi-plus-circle me-2"></i> Add Waste
                    </a>
                @else
                    <a href="{{ route('wastes.index') }}" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-x-circle me-2"></i> Clear Filters
                    </a>
                @endif
            </div>
        </div>
    @else
        <div class="table-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100 mb-0">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th><a class="sort-link" href="{{ $sortLink('id') }}">ID {!! $sortIcon('id') !!}</a></th>
                                <th><a class="sort-link" href="{{ $sortLink('type') }}">Type {!! $sortIcon('type') !!}</a></th>
                                <th><a class="sort-link" href="{{ $sortLink('weight') }}">Weight {!! $sortIcon('weight') !!}</a></th>
                                <th><i class="bi bi-tag"></i> Category</th>
                                <th><i class="bi bi-geo-alt"></i> Collection Point</th>
                                <th><i class="bi bi-person"></i> User</th>
                                <th><a class="sort-link" href="{{ $sortLink('status') }}">Status {!! $sortIcon('status') !!}</a></th>
                                <th><a class="sort-link" href="{{ $sortLink('created_at') }}">Created {!! $sortIcon('created_at') !!}</a></th>
                                <th><i class="bi bi-gear"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($wastes as $waste)
                                <tr class="text-center">
                                    <td>{{ $waste->id }}</td>
                                    <td>
                                        <div class="fw-medium">{{ $waste->type }}</div>
                                        <small class="text-muted d-block text-truncate" style="max-width: 180px;" title="{{ $waste->description }}">
                                            {{ $waste->description ?: 'No description' }}
                                        </small>
                                    </td>
                                    <td class="fw-medium">{{ number_format((float) $waste->weight, 2) }} kg</td>
                                    <td>{{ $waste->category->name ?? 'Uncategorized' }}</td>
                                    <td>{{ $waste->collectionPoint->name ?? 'Not assigned' }}</td>
                                    <td>{{ $waste->user->name ?? 'Unknown user' }}</td>
                                    <td>
                                        @if($waste->status === 'recyclable')
                                            <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25">
                                                <i class="bi bi-arrow-repeat me-1 small"></i> Recyclable
                                            </span>
                                        @else
                                            <span class="badge bg-info bg-opacity-15 text-info border border-info border-opacity-25">
                                                <i class="bi bi-arrow-counterclockwise me-1 small"></i> Reusable
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ optional($waste->created_at)->format('Y-m-d') ?? '—' }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('wastes.show', $waste->id) }}"
                                               class="action-btn action-view"
                                               data-bs-toggle="tooltip"
                                               title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('wastes.edit', $waste->id) }}"
                                               class="action-btn action-edit"
                                               data-bs-toggle="tooltip"
                                               title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('wastes.destroy', $waste->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="action-btn action-delete"
                                                        onclick="return confirm('Delete this waste record?')"
                                                        data-bs-toggle="tooltip"
                                                        title="Delete">
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
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 gap-2">
            <div class="text-muted small">
                Showing {{ $wastes->firstItem() }}–{{ $wastes->lastItem() }}
                of {{ $wastes->total() }} entries
            </div>
            {{ $wastes->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

{{-- Import Modal --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('wastes.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="bi bi-upload me-2"></i>Import Wastes
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
                    <p class="text-muted small mb-0">
                        The first line may contain headers (type, weight, status, description,
                        waste_category_id, user_id, collection_point_id).
                        Use the CSV export as a template. Separator <code>;</code> or <code>,</code> is detected automatically.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-upload me-1"></i>Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

        @if ($errors->has('csv_file'))
            new bootstrap.Modal(document.getElementById('importModal')).show();
        @endif
    });
</script>
@endsection