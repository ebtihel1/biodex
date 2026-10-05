@extends('back.layout')

@section('title', 'Waste List')

@section('content')
<div class="container-fluid px-0">
    <!-- En-tête amélioré avec statistiques -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <h1 class="page-title text-success mb-2">
                        <i class="bi bi-recycle me-2"></i> Waste Inventory
                    </h1>
                    <p class="text-muted mb-0">Review, filter and manage recorded waste items</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('wastes.create') }}" class="btn btn-success shadow-sm fw-medium px-4 py-2">
                        <i class="bi bi-plus-circle me-2"></i> Add Waste
                    </a>
                </div>
            </div>
        </div>

        <!-- Cartes de statistiques améliorées -->
        <div class="col-12">
            <div class="stats-wrapper mb-4">
                <div class="campaign-stats">
                    <div class="stat-card bg-gradient-primary">
                        <i class="bi bi-box-seam stat-icon"></i>
                        <div class="stat-number">{{ number_format((int) $summary->total_count) }}</div>
                        <div class="stat-label">Total Items</div>
                    </div>

                    <div class="stat-card bg-gradient-success">
                        <i class="bi bi-speedometer2 stat-icon"></i>
                        <div class="stat-number">{{ number_format((float) $summary->total_weight, 2) }} kg</div>
                        <div class="stat-label">Total Recorded Weight</div>
                    </div>

                    <div class="stat-card bg-gradient-warning">
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
        </div>
    </div>

    {{-- Messages flash --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5 flex-shrink-0"></i>
            <div class="flex-grow-1">
                <span class="fw-medium">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close flex-shrink-0" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Barre de filtres -->
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 text-success fw-medium">
                <i class="bi bi-funnel me-2"></i> Filter Waste Items
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('wastes.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label for="waste-search" class="form-label small text-muted fw-medium">Search inventory</label>
                    <input id="waste-search" type="search" name="search" value="{{ $search }}" 
                           class="form-control" placeholder="Type, description, category, user or point">
                </div>
                <div class="col-12 col-md-2">
                    <label for="waste-status" class="form-label small text-muted fw-medium">Status</label>
                    <select id="waste-status" name="status" class="form-select">
                        <option value="">All statuses</option>
                        <option value="recyclable" @selected($status === 'recyclable')>Recyclable</option>
                        <option value="reusable" @selected($status === 'reusable')>Reusable</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label for="waste-category" class="form-label small text-muted fw-medium">Category</label>
                    <select id="waste-category" name="category" class="form-select">
                        <option value="">All categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) $categoryId === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-search me-1"></i> Apply
                    </button>
                    @if($search !== '' || $status !== '' || $categoryId !== '')
                        <a href="{{ route('wastes.index') }}" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-x-circle me-1"></i> Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if ($wastes->isEmpty())
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                <h4 class="text-muted mb-3">No waste items found</h4>
                <p class="text-muted mb-4">
                    {{ $search !== '' || $status !== '' || $categoryId !== '' 
                        ? 'Try changing or clearing your filters.' 
                        : 'Add a waste record to start building the inventory.' }}
                </p>
                @if($search === '' && $status === '' && $categoryId === '')
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
        <!-- Tableau amélioré -->
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <h5 class="card-title mb-2 mb-md-0 text-success fw-medium">
                        <i class="bi bi-list-ul me-2"></i> Waste List
                    </h5>
                    <div class="text-muted small">
                        Total: {{ number_format($wastes->total()) }} record(s)
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-success text-center">
                            <tr>
                                <th>Waste Item</th>
                                <th>Weight</th>
                                <th>Category</th>
                                <th>Collection Point</th>
                                <th>Submitted By</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($wastes as $waste)
                                <tr class="text-center">
                                    <td>
                                        <div class="fw-medium">{{ $waste->type }}</div>
                                        <small class="text-muted d-block text-truncate" style="max-width: 200px;" title="{{ $waste->description }}">
                                            {{ $waste->description ?: 'No description' }}
                                        </small>
                                    </td>
                                    <td class="fw-medium">{{ number_format((float) $waste->weight, 2) }} kg</td>
                                    <td>{{ $waste->category->name ?? 'Uncategorized' }}</td>
                                    <td>{{ $waste->collectionPoint->name ?? 'Not assigned' }}</td>
                                    <td>{{ $waste->user->name ?? 'Unknown user' }}</td>
                                    <td>
                                        @if($waste->status === 'recyclable')
                                            <span class="badge bg-success-subtle text-success-emphasis px-3 py-2">
                                                <i class="bi bi-arrow-repeat me-1"></i> Recyclable
                                            </span>
                                        @else
                                            <span class="badge bg-info-subtle text-info-emphasis px-3 py-2">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reusable
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- View -->
                                            <a href="{{ route('wastes.show', $waste->id) }}" 
                                               class="action-btn action-view" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <!-- Edit -->
                                            <a href="{{ route('wastes.edit', $waste->id) }}" 
                                               class="action-btn action-edit" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <!-- Delete -->
                                            <form action="{{ route('wastes.destroy', $waste->id) }}" method="POST" 
                                                  onsubmit="return confirm('Delete this waste record?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn action-delete" title="Delete">
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

        <!-- Pagination en fin de page -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mt-4 mb-4 px-1">
            <span class="text-muted small">
                <i class="bi bi-info-circle me-1"></i>
                Showing <strong>{{ $wastes->firstItem() ?? 0 }}</strong>–<strong>{{ $wastes->lastItem() ?? 0 }}</strong> of <strong>{{ number_format($wastes->total()) }}</strong> records
            </span>
            <div class="pagination-wrapper">
                {{ $wastes->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

<!-- Styles réutilisables depuis la page Categories -->
<style>
.page-title { font-weight: 600; font-size: 1.75rem; }
.card { border: none; transition: transform 0.2s ease-in-out; }
.card:hover { transform: translateY(-1px); }
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; }
.table th { border-bottom: 2px solid #198754; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; padding: 1rem 0.75rem; font-size: 0.75rem; }
.table tbody tr:hover { background-color: rgba(25, 135, 84, 0.04); box-shadow: inset 0 0 0 1px rgba(25, 135, 84, 0.1); }
.action-btn { display:inline-flex; align-items:center; justify-content:center; width:2.5rem; height:2.5rem; border-radius:8px; border:none; text-decoration:none; transition:all 0.3s ease; position:relative; overflow:hidden; color:#fff!important; }
.action-view { background: linear-gradient(135deg,#0dcaf0,#0aa2c0); }
.action-view:hover { background: linear-gradient(135deg,#0aa2c0,#087990); }
.action-edit { background: linear-gradient(135deg,#ffc107,#e0a800); }
.action-edit:hover { background: linear-gradient(135deg,#e0a800,#c69500); }
.action-delete { background: linear-gradient(135deg,#dc3545,#c82333); }
.action-delete:hover { background: linear-gradient(135deg,#c82333,#a71e2a); }

/* Statistics Section Styles */
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

/* Gradient backgrounds for cards */
.bg-gradient-primary {
    background: linear-gradient(135deg, #f8e3a3ff, #edd58bff) !important;
}

.bg-gradient-success {
    background: linear-gradient(135deg, #f8e3a3ff, #edd58bff) !important;
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #f8e3a3ff, #edd58bff) !important;
}

/* Pagination Styles */
.pagination-wrapper .pagination {
    margin: 0;
    gap: 0.35rem;
}

.pagination-wrapper .page-link {
    border: 1px solid rgba(25, 135, 84, 0.15);
    border-radius: 8px !important;
    color: #198754;
    font-size: 0.85rem;
    font-weight: 500;
    padding: 0.45rem 0.75rem;
    transition: all 0.2s ease;
    min-width: 38px;
    text-align: center;
}

.pagination-wrapper .page-link:hover {
    background: linear-gradient(135deg, #f8e3a3, #edd58b);
    border-color: #e8c471;
    color: #1b4332;
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(25, 135, 84, 0.15);
}

.pagination-wrapper .page-item.active .page-link {
    background: linear-gradient(135deg, #198754, #146c43);
    border-color: #198754;
    color: #fff;
    box-shadow: 0 3px 10px rgba(25, 135, 84, 0.3);
}

.pagination-wrapper .page-item.disabled .page-link {
    background: #f8f9fa;
    border-color: #e9ecef;
    color: #adb5bd;
}

.pagination-wrapper .page-item:first-child .page-link,
.pagination-wrapper .page-item:last-child .page-link {
    border-radius: 8px !important;
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

@media (max-width: 576px) {
    .pagination-wrapper .page-link {
        padding: 0.35rem 0.55rem;
        font-size: 0.75rem;
        min-width: 32px;
    }
}
</style>
@endsection