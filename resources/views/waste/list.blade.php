@extends('back.layout')

@section('title', 'Waste List')

@section('content')
<div class="container-fluid px-0">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <h1 class="page-title text-success mb-2">
                        <i class="bi bi-recycle me-2"></i> Waste List ♻️
                    </h1>
                    <p class="text-muted mb-0">Manage all the wastes on your platform</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('wastes.create') }}" class="btn btn-success shadow-sm fw-medium px-4 py-2">
                        <i class="bi bi-plus-circle me-2"></i> Add Waste
                    </a>
                </div>
            </div>
        </div>

        <!-- Barre de recherche -->
        <div class="col-12 mb-4">
            <form action="{{ route('wastes.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" placeholder="Search by type or category"
                       value="{{ request('search') }}"
                       class="form-control" style="max-width:500px;">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
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

    @if ($wastes->isEmpty())
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                <h4 class="text-muted mb-3">No wastes found</h4>
                <p class="text-muted mb-4">Start by adding your first waste</p>
                <a href="{{ route('wastes.create') }}" class="btn btn-success px-4">
                    <i class="bi bi-plus-circle me-2"></i> Create Waste
                </a>
            </div>
        </div>
    @else
        <!-- Tableau amélioré -->
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-center">
                        <thead class="table-success">
                            <tr>
                                <th>Type</th>
                                <th>Weight (kg)</th>
                                <th>Status</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th>User</th>
                                <th>Collection Point</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($wastes as $waste)
                                <tr>
                                    <td>{{ $waste->type }}</td>
                                    <td>{{ $waste->weight }}</td>
                                    <td>{{ $waste->status }}</td>
                                    <td>{{ $waste->description ?? 'No description' }}</td>
                                    <td>{{ $waste->category->name ?? 'Unknown' }}</td>
                                    <td>{{ $waste->user->name ?? 'Unknown User' }}</td>
                                    <td>{{ $waste->collectionPoint->name ?? 'Unknown' }}</td>

                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('wastes.show', $waste->id) }}" class="action-btn action-info" title="Show">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('wastes.edit', $waste->id) }}" class="action-btn action-edit" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('wastes.destroy', $waste->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
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

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $wastes->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Styles réutilisables -->
<style>
.page-title { font-weight: 600; font-size: 1.75rem; }
.card { border: none; transition: transform 0.2s ease-in-out; }
.card:hover { transform: translateY(-1px); }
.table th { border-bottom: 2px solid #198754; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; padding: 1rem 0.75rem; }
.table tbody tr:hover { background-color: rgba(25, 135, 84, 0.04); box-shadow: inset 0 0 0 1px rgba(25, 135, 84, 0.1); }
.action-btn { display:inline-flex; align-items:center; justify-content:center; width:2.5rem; height:2.5rem; border-radius:8px; border:none; text-decoration:none; transition:all 0.3s ease; position:relative; overflow:hidden; color:#fff!important; }
.action-info { background: linear-gradient(135deg,#0dcaf0,#31d2f2); }
.action-info:hover { background: linear-gradient(135deg,#31d2f2,#0bb8e0); }
.action-edit { background: linear-gradient(135deg,#ffc107,#e0a800); }
.action-edit:hover { background: linear-gradient(135deg,#e0a800,#c69500); }
.action-delete { background: linear-gradient(135deg,#dc3545,#c82333); }
.action-delete:hover { background: linear-gradient(135deg,#c82333,#a71e2a); }
</style>
@endsection
