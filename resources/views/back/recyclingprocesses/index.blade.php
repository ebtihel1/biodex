@extends('back.layout')

@section('title', 'Recycling Processes')

@section('content')
<div class="container-fluid px-0">
    <!-- En-tête avec statistiques -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <h1 class="page-title text-success mb-2">
                        <i class="bi bi-arrow-repeat me-2"></i> Recycling Processes
                    </h1>
                    <p class="text-muted mb-0">Manage the transformation of waste into recycled products</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('recyclingprocesses.create') }}" class="btn btn-success shadow-sm fw-medium px-4 py-2">
                        <i class="bi bi-plus-circle me-2"></i> New Process
                    </a>
                </div>
            </div>
        </div>

        <!-- Cartes de statistiques -->
        <div class="col-12">
            <div class="row g-3 mb-4">
                @php
                    $totalProcesses = $recyclingProcesses->count();
                    $pending = $recyclingProcesses->where('status', 'pending')->count();
                    $inProgress = $recyclingProcesses->where('status', 'in_progress')->count();
                    $completed = $recyclingProcesses->where('status', 'completed')->count();
                    $failed = $recyclingProcesses->where('status', 'failed')->count();
                @endphp

                <div class="col-md-3 col-sm-6">
                    <div class="card stat-card border-0 rounded-3 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Total Processes</h6>
                                    <h3 class="text-success mb-0">{{ $totalProcesses }}</h3>
                                </div>
                                <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                                    <i class="bi bi-arrow-repeat text-success fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="card stat-card border-0 rounded-3 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title text-muted mb-2">In Progress</h6>
                                    <h3 class="text-primary mb-0">{{ $inProgress }}</h3>
                                </div>
                                <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                                    <i class="bi bi-hourglass-split text-primary fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="card stat-card border-0 rounded-3 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Completed</h6>
                                    <h3 class="text-info mb-0">{{ $completed }}</h3>
                                </div>
                                <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                                    <i class="bi bi-check-circle text-info fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="card stat-card border-0 rounded-3 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Pending</h6>
                                    <h3 class="text-warning mb-0">{{ $pending }}</h3>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                                    <i class="bi bi-clock text-warning fs-5"></i>
                                </div>
                            </div>
                        </div>
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

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4 d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5 flex-shrink-0"></i>
            <div class="flex-grow-1">
                <span class="fw-medium">{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close flex-shrink-0" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($recyclingProcesses->isEmpty())
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                <h4 class="text-muted mb-3">No recycling processes found</h4>
                <p class="text-muted mb-4">Start by creating your first recycling process</p>
                <a href="{{ route('recyclingprocesses.create') }}" class="btn btn-success px-4">
                    <i class="bi bi-plus-circle me-2"></i> Create Process
                </a>
            </div>
        </div>
    @else
        <!-- Tableau -->
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <h5 class="card-title mb-2 mb-md-0 text-success fw-medium">
                        <i class="bi bi-list-ul me-2"></i> Process List
                    </h5>
                    <div class="text-muted small">
                        Total: {{ $recyclingProcesses->count() }} processes
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-success">
                            <tr>
                                <th class="ps-4">Waste</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th class="d-none d-md-table-cell">Start Date</th>
                                <th class="d-none d-lg-table-cell">End Date</th>
                                <th class="d-none d-lg-table-cell">Output Quantity</th>
                                <th class="d-none d-lg-table-cell">Responsible</th>
                                <th class="text-center pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recyclingProcesses as $process)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                                <i class="bi bi-trash text-success"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-medium text-dark">{{ $process->waste->type ?? 'N/A' }}</h6>
                                                <small class="text-muted">{{ $process->waste->category->name ?? 'Uncategorized' }}</small>
                                            </div>
                                        </div>
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
                                                'failed' => 'danger'
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Pending',
                                                'in_progress' => 'In Progress',
                                                'completed' => 'Completed',
                                                'failed' => 'Failed'
                                            ];
                                            $color = $statusColors[$process->status] ?? 'secondary';
                                            $label = $statusLabels[$process->status] ?? $process->status;
                                        @endphp
                                        <span class="badge bg-{{ $color }} bg-opacity-15 text-{{ $color }} border border-{{ $color }} border-opacity-25">
                                            {{ $label }}
                                        </span>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>
                                            {{ $process->start_date ? $process->start_date->format('d/m/Y') : 'N/A' }}
                                        </small>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <small class="text-muted">
                                            <i class="bi bi-calendar-check me-1"></i>
                                            {{ $process->end_date ? $process->end_date->format('d/m/Y') : 'In Progress' }}
                                        </small>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        @if($process->output_quantity)
                                            <span class="badge bg-info bg-opacity-15 text-info">
                                                {{ $process->output_quantity }} kg
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <small class="text-muted">
                                            {{ $process->responsibleUser->name ?? 'Unassigned' }}
                                        </small>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="action-buttons">
                                            <!-- Edit -->
                                            <a href="{{ route('recyclingprocesses.edit', $process->id) }}" 
                                               class="action-btn action-edit" 
                                               data-bs-toggle="tooltip" 
                                               title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <!-- Delete -->
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
    @endif
</div>

<style>
.page-title { font-weight: 600; font-size: 1.75rem; }
.card { border: none; transition: transform 0.2s ease-in-out; }
.card:hover { transform: translateY(-1px); }
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; }
.table th { border-bottom: 2px solid #198754; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; padding: 1rem 0.75rem; }
.table tbody tr:hover { background-color: rgba(25, 135, 84, 0.04); box-shadow: inset 0 0 0 1px rgba(25, 135, 84, 0.1); }
.action-buttons { display: flex; gap: 0.5rem; justify-content: center; align-items: center; }
.action-btn { display:inline-flex; align-items:center; justify-content:center; width:2.5rem; height:2.5rem; border-radius:8px; border:none; text-decoration:none; transition:all 0.3s ease; position:relative; overflow:hidden; color:#fff!important; }
.action-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
.action-edit { background: linear-gradient(135deg,#ffc107,#e0a800); }
.action-edit:hover { background: linear-gradient(135deg,#e0a800,#c69500); }
.action-delete { background: linear-gradient(135deg,#dc3545,#c82333); }
.action-delete:hover { background: linear-gradient(135deg,#c82333,#a71e2a); }
</style>
@endsection

