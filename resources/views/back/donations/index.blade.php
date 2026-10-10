@extends('back.layout')

@section('title', 'Manage Donations')

@section('content')
<style>
/* ===== Statistics (uniformisé) ===== */
.donation-stats {
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
    top: -40%; right: -40%;
    width: 200%; height: 200%;
    background: radial-gradient(circle at top right, rgba(255,255,255,0.2), transparent 70%);
    transform: rotate(25deg);
}
.stats-wrapper .stat-icon { font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.9; color: #1b4332; }
.stats-wrapper .stat-number { font-size: 2rem; font-weight: 700; color: #1b4332; }
.stats-wrapper .stat-label { opacity: 0.9; font-size: 0.95rem; letter-spacing: 0.5px; color: #1b4332; }

/* ===== Table Card ===== */
.table-card {
    background: #ffffff;
    border-radius: 15px;
    box-shadow: 0 6px 30px rgba(0,0,0,0.08);
    overflow: hidden;
    border: none;
}
.table thead th { text-align: center; padding: 1rem; vertical-align: middle; }
.table tbody tr { transition: all 0.25s ease; }
.table tbody tr:hover { background-color: rgba(25, 135, 84, 0.05); }
.table td { vertical-align: middle; text-align: center; padding: 0.9rem; }

.table thead th a.sort-link {
    color: #198754; text-decoration: none; font-weight: 600; white-space: nowrap;
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
    display: inline-flex; align-items: center; justify-content: center;
    width: 2.5rem; height: 2.5rem; border-radius: 8px; border: none;
    text-decoration: none; transition: all 0.3s ease; color: #fff !important;
    position: relative; overflow: hidden; padding: 0; line-height: 1;
}
.action-btn i { font-size: 1rem; line-height: 1; display: inline-block; transition: transform 0.2s ease; }
.action-btn:hover i { transform: scale(1.1); }
.action-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

.action-view   { background: linear-gradient(135deg,#0dcaf0,#0aa2c0); }
.action-view:hover { background: linear-gradient(135deg,#0aa2c0,#087990); }
.action-edit   { background: linear-gradient(135deg,#ffc107,#e0a800); }
.action-edit:hover { background: linear-gradient(135deg,#e0a800,#c69500); }
.action-delete { background: linear-gradient(135deg,#dc3545,#c82333); }
.action-delete:hover { background: linear-gradient(135deg,#c82333,#a71e2a); }

/* ===== Page Header ===== */
.page-header {
    display: flex; flex-wrap: wrap; justify-content: space-between;
    align-items: center; margin-bottom: 1.5rem;
}
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
        request()->only('search', 'status', 'condition', 'waste_category_id', 'date_from', 'date_to'),
        fn ($value) => $value !== null && $value !== ''
    );
@endphp

<div class="container-fluid py-4 fade-in-up">

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

    {{-- Statistics --}}
    <div class="stats-wrapper fade-in-up">
        <div class="donation-stats">
            <div class="stat-card">
                <i class="bi bi-gift stat-icon"></i>
                <div class="stat-number">{{ number_format((int) $summary->total_count) }}</div>
                <div class="stat-label">Total Donations</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-check-circle stat-icon"></i>
                <div class="stat-number">{{ number_format((int) $summary->available_count) }}</div>
                <div class="stat-label">Available</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-clock-history stat-icon"></i>
                <div class="stat-number">{{ number_format((int) $summary->pending_count) }}</div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-check2-all stat-icon"></i>
                <div class="stat-number">{{ number_format((int) $summary->accepted_count) }}</div>
                <div class="stat-label">Accepted</div>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <div class="page-header">
        <div>
            <h2 class="fw-bold text-success mb-1">
                <i class="bi bi-gift me-2"></i>Manage Donations
            </h2>
            <p class="text-muted mb-0">Track, filter and manage customer donations</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route($exportCsvRoute, $exportParams) }}" class="btn btn-outline-success">
                <i class="bi bi-filetype-csv me-1"></i>Export CSV
            </a>
            <a href="{{ route($exportPdfRoute, $exportParams) }}" class="btn btn-outline-danger">
                <i class="bi bi-filetype-pdf me-1"></i>Export PDF
            </a>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-upload me-1"></i>Import CSV
            </button>
            <a href="{{ route($createRoute) }}" class="btn btn-add">
                <i class="bi bi-plus-circle-fill me-2"></i>New Donation
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('back.donations.index') }}" class="filter-bar">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label class="form-label mb-1 small text-muted">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control" placeholder="User, item, description...">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1 small text-muted">Category</label>
                <select name="waste_category_id" class="form-select">
                    <option value="">All categories</option>
                    @if(isset($wasteCategories) && is_array($wasteCategories))
                        @foreach($wasteCategories as $id => $name)
                            <option value="{{ $id }}" @selected((string) request('waste_category_id') === (string) $id)>{{ $name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1 small text-muted">Condition</label>
                <select name="condition" class="form-select">
                    <option value="">All conditions</option>
                    <option value="new"     @selected(request('condition') === 'new')>New</option>
                    <option value="used"    @selected(request('condition') === 'used')>Used</option>
                    <option value="damaged" @selected(request('condition') === 'damaged')>Damaged</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1 small text-muted">Status</label>
                <select name="status" class="form-select">
                    <option value="">All statuses</option>
                    @foreach(\App\Enums\DonationStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(request('status') == $status->value)>{{ ucfirst($status->value) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1 small text-muted">Date from</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1 small text-muted">Date to</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
            </div>
            <div class="col-12 col-md-1 d-flex gap-2">
                <button type="submit" class="btn btn-success flex-fill" title="Filter">
                    <i class="bi bi-funnel-fill"></i>
                </button>
                <a href="{{ route('back.donations.index') }}" class="btn btn-outline-secondary" title="Reset">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </div>
    </form>

    @if ($donations->isEmpty())
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                <h4 class="text-muted mb-3">No donations found</h4>
                <p class="text-muted mb-4">
                    {{ request('search') || request('status') || request('condition') || request('waste_category_id') || request('date_from') || request('date_to')
                        ? 'Try changing or clearing your filters.'
                        : 'Create your first donation to get started.' }}
                </p>
                @if(!request('search') && !request('status') && !request('condition') && !request('waste_category_id') && !request('date_from') && !request('date_to'))
                    <a href="{{ route($createRoute) }}" class="btn btn-success px-4">
                        <i class="bi bi-plus-circle me-2"></i> Create Donation
                    </a>
                @else
                    <a href="{{ route('back.donations.index') }}" class="btn btn-outline-secondary px-4">
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
                                <th><a class="sort-link" href="{{ $sortLink('user_id') }}">User {!! $sortIcon('user_id') !!}</a></th>
                                <th>Category</th>
                                <th><a class="sort-link" href="{{ $sortLink('item_name') }}">Item {!! $sortIcon('item_name') !!}</a></th>
                                <th><a class="sort-link" href="{{ $sortLink('condition') }}">Condition {!! $sortIcon('condition') !!}</a></th>
                                <th>Sentiment</th>
                                <th><a class="sort-link" href="{{ $sortLink('status') }}">Status {!! $sortIcon('status') !!}</a></th>
                                <th><i class="bi bi-gear"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($donations as $donation)
                                <tr class="text-center">
                                    <td>{{ $donation->id }}</td>
                                    <td>{{ $donation->user->name ?? 'Guest' }}</td>
                                    <td>{{ $donation->waste->category->name ?? 'N/A' }}</td>
                                    <td>{{ $donation->item_name }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ ucfirst($donation->condition) }}</span>
                                    </td>
                                    <td>
                                        @if(isset($sentiments[$donation->id]))
                                            <span class="badge bg-{{ $sentiments[$donation->id] === 'positive' ? 'success' : ($sentiments[$donation->id] === 'negative' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($sentiments[$donation->id]) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusValue = is_object($donation->status) ? $donation->status->value : $donation->status;
                                            $statusColors = [
                                                'available' => 'success',
                                                'pending'   => 'warning',
                                                'accepted'  => 'info',
                                                'rejected'  => 'danger',
                                                'completed' => 'primary',
                                            ];
                                            $color = $statusColors[$statusValue] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $color }} bg-opacity-15 text-{{ $color }} border border-{{ $color }} border-opacity-25">
                                            {{ ucfirst($statusValue) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('back.donations.show', $donation) }}"
                                               class="action-btn action-view"
                                               data-bs-toggle="tooltip"
                                               title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('back.donations.edit', $donation) }}"
                                               class="action-btn action-edit"
                                               data-bs-toggle="tooltip"
                                               title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('back.donations.destroy', $donation) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="action-btn action-delete"
                                                        onclick="return confirm('Are you sure you want to delete this donation?')"
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
                Showing {{ $donations->firstItem() }}–{{ $donations->lastItem() }}
                of {{ $donations->total() }} entries
            </div>
            {{ $donations->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

{{-- Import Modal --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route($importRoute) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="bi bi-upload me-2"></i>Import Donations
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
                        Headers may contain (user_id, waste_category_id, item_name, condition,
                        description, status, pickup_required, pickup_address).
                        Separator <code>;</code> or <code>,</code> is detected automatically.
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