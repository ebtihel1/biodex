@extends('back.layout')

@section('title', 'Recycling Processes')

@section('content')
<style>
/* ===== Statistics (uniformisé avec les autres modules) ===== */
.process-stats {
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
.table thead th { text-align: center; padding: 1rem; vertical-align: middle; }
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
    display:inline-flex; align-items:center; justify-content:center;
    width:2.5rem; height:2.5rem; border-radius:8px; border:none;
    text-decoration:none; transition:all 0.3s ease; color:#fff!important;
}
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

/* ===== Responsive ===== */
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
        request()->only('search', 'status', 'responsible', 'waste'),
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

    {{-- Statistics (uniformisé) --}}
    <div class="stats-wrapper fade-in-up">
        <div class="process-stats">
            <div class="stat-card">
                <i class="bi bi-arrow-repeat stat-icon"></i>
                <div class="stat-number">{{ number_format((int) $summary->total_count) }}</div>
                <div class="stat-label">Total Processes</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-hourglass-split stat-icon"></i>
                <div class="stat-number">{{ number_format((int) $summary->in_progress_count) }}</div>
                <div class="stat-label">In Progress</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-check-circle stat-icon"></i>
                <div class="stat-number">{{ number_format((int) $summary->completed_count) }}</div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-clock stat-icon"></i>
                <div class="stat-number">{{ number_format((int) $summary->pending_count) }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
    </div>
    
    {{-- Header --}}
    <div class="page-header">
        <div>
            <h2 class="fw-bold text-success mb-1">
                <i class="bi bi-arrow-repeat me-2"></i>Recycling Processes
            </h2>
            <p class="text-muted mb-0">Manage the transformation of waste into recycled products</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('recyclingprocesses.export.csv', $exportParams) }}" class="btn btn-outline-success">
                <i class="bi bi-filetype-csv me-1"></i>Export CSV
            </a>
            <a href="{{ route('recyclingprocesses.export.pdf', $exportParams) }}" class="btn btn-outline-danger">
                <i class="bi bi-filetype-pdf me-1"></i>Export PDF
            </a>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-upload me-1"></i>Import CSV
            </button>
            <a href="{{ route('recyclingprocesses.create') }}" class="btn btn-add">
                <i class="bi bi-plus-circle-fill me-2"></i>New Process
            </a>
        </div>
    </div>
    {{-- Filters --}}
    <form method="GET" action="{{ route('recyclingprocesses.index') }}" class="filter-bar">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label mb-1 small text-muted">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control" placeholder="Method, waste, category, user...">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1 small text-muted">Status</label>
                <select name="status" class="form-select">
                    <option value="">All statuses</option>
                    <option value="pending"     @selected(request('status') === 'pending')>Pending</option>
                    <option value="in_progress" @selected(request('status') === 'in_progress')>In Progress</option>
                    <option value="completed"   @selected(request('status') === 'completed')>Completed</option>
                    <option value="failed"      @selected(request('status') === 'failed')>Failed</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1 small text-muted">Waste</label>
                <select name="waste" class="form-select">
                    <option value="">All wastes</option>
                    @foreach ($wastes as $waste)
                        <option value="{{ $waste->id }}" @selected((string) request('waste') === (string) $waste->id)>
                            {{ $waste->type }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1 small text-muted">Responsible</label>
                <select name="responsible" class="form-select">
                    <option value="">All users</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected((string) request('responsible') === (string) $user->id)>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-success flex-fill">
                    <i class="bi bi-funnel-fill me-1"></i>Filter
                </button>
                <a href="{{ route('recyclingprocesses.index') }}" class="btn btn-outline-secondary" title="Reset filters">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </div>
    </form>

    @if ($recyclingProcesses->isEmpty())
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                <h4 class="text-muted mb-3">No recycling processes found</h4>
                <p class="text-muted mb-4">
                    {{ request('search') || request('status') || request('waste') || request('responsible')
                        ? 'Try changing or clearing your filters.'
                        : 'Start by creating your first recycling process' }}
                </p>
                @if(!request('search') && !request('status') && !request('waste') && !request('responsible'))
                    <a href="{{ route('recyclingprocesses.create') }}" class="btn btn-success px-4">
                        <i class="bi bi-plus-circle me-2"></i> Create Process
                    </a>
                @else
                    <a href="{{ route('recyclingprocesses.index') }}" class="btn btn-outline-secondary px-4">
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
                                <th><a class="sort-link" href="{{ $sortLink('waste_id') }}">Waste {!! $sortIcon('waste_id') !!}</a></th>
                                <th><a class="sort-link" href="{{ $sortLink('method') }}">Method {!! $sortIcon('method') !!}</a></th>
                                <th><a class="sort-link" href="{{ $sortLink('status') }}">Status {!! $sortIcon('status') !!}</a></th>
                                <th><a class="sort-link" href="{{ $sortLink('start_date') }}">Start Date {!! $sortIcon('start_date') !!}</a></th>
                                <th><a class="sort-link" href="{{ $sortLink('end_date') }}">End Date {!! $sortIcon('end_date') !!}</a></th>
                                <th><a class="sort-link" href="{{ $sortLink('output_quantity') }}">Qty (kg) {!! $sortIcon('output_quantity') !!}</a></th>
                                <th><a class="sort-link" href="{{ $sortLink('responsible_user_id') }}">Responsible {!! $sortIcon('responsible_user_id') !!}</a></th>
                                <th><i class="bi bi-gear"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recyclingProcesses as $process)
                                <tr class="text-center">
                                    <td>{{ $process->id }}</td>
                                    <td>
                                        <div class="fw-medium">{{ $process->waste->type ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $process->waste->category->name ?? 'Uncategorized' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $process->method }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'in_progress' => 'primary',
                                                'completed' => 'success',
                                                'failed' => 'danger',
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Pending',
                                                'in_progress' => 'In Progress',
                                                'completed' => 'Completed',
                                                'failed' => 'Failed',
                                            ];
                                            $color = $statusColors[$process->status] ?? 'secondary';
                                            $label = $statusLabels[$process->status] ?? $process->status;
                                        @endphp
                                        <span class="badge bg-{{ $color }} bg-opacity-15 text-{{ $color }} border border-{{ $color }} border-opacity-25">
                                            {{ $label }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>
                                            {{ $process->start_date ? $process->start_date->format('d/m/Y') : 'N/A' }}
                                        </small>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar-check me-1"></i>
                                            {{ $process->end_date ? $process->end_date->format('d/m/Y') : 'In Progress' }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($process->output_quantity)
                                            <span class="badge bg-info bg-opacity-15 text-info">
                                                {{ $process->output_quantity }} kg
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $process->responsibleUser->name ?? 'Unassigned' }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('recyclingprocesses.show', $process->id) }}"
                                               class="action-btn action-view"
                                               data-bs-toggle="tooltip"
                                               title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('recyclingprocesses.edit', $process->id) }}"
                                               class="action-btn action-edit"
                                               data-bs-toggle="tooltip"
                                               title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('recyclingprocesses.destroy', $process->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="action-btn action-delete"
                                                        onclick="return confirm('Are you sure you want to delete this process?')"
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
                Showing {{ $recyclingProcesses->firstItem() }}–{{ $recyclingProcesses->lastItem() }}
                of {{ $recyclingProcesses->total() }} entries
            </div>
            {{ $recyclingProcesses->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

{{-- Import Modal --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('recyclingprocesses.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="bi bi-upload me-2"></i>Import Recycling Processes
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
                        The first line may contain headers (waste_id, method, status, start_date, end_date,
                        output_quantity, output_quality, responsible_user_id, notes).
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