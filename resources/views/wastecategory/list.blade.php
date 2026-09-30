@extends('back.layout')

@section('title', 'Waste Categories')

@section('content')
<div class="container-fluid px-0">
    <!-- En-tête amélioré avec statistiques -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <h1 class="page-title text-success mb-2">
                        <i class="bi bi-recycle me-2"></i> Waste Categories
                    </h1>
                    <p class="text-muted mb-0">Manage all the waste categories on your platform</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('waste_categories.create') }}" class="btn btn-success shadow-sm fw-medium px-4 py-2">
                        <i class="bi bi-plus-circle me-2"></i> Add Category
                    </a>
                </div>
            </div>
        </div>

        <!-- Cartes de statistiques améliorées -->
        <div class="col-12">
            <div class="stats-wrapper mb-4">
                <div class="campaign-stats">
                    @php
                        $totalCategories = $categories->count();
                        $withInstructions = $categories->whereNotNull('recycling_instructions')->count();
                        $withoutInstructions = $totalCategories - $withInstructions;
                    @endphp

                    <div class="stat-card bg-gradient-primary">
                        <i class="bi bi-recycle stat-icon"></i>
                        <div class="stat-number">{{ $totalCategories }}</div>
                        <div class="stat-label">Total Categories</div>
                    </div>

                    <div class="stat-card bg-gradient-success">
                        <i class="bi bi-check-circle-fill stat-icon"></i>
                        <div class="stat-number">{{ $withInstructions }}</div>
                        <div class="stat-label">With Instructions</div>
                    </div>

                    <div class="stat-card bg-gradient-warning">
                        <i class="bi bi-exclamation-circle stat-icon"></i>
                        <div class="stat-number">{{ $withoutInstructions }}</div>
                        <div class="stat-label">Without Instructions</div>
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

    @if ($categories->isEmpty())
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                <h4 class="text-muted mb-3">No categories found</h4>
                <p class="text-muted mb-4">Start by adding your first waste category</p>
                <a href="{{ route('waste_categories.create') }}" class="btn btn-success px-4">
                    <i class="bi bi-plus-circle me-2"></i> Create Category
                </a>
            </div>
        </div>
    @else
        <!-- Tableau amélioré -->
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <h5 class="card-title mb-2 mb-md-0 text-success fw-medium">
                        <i class="bi bi-list-ul me-2"></i> Category List
                    </h5>
                    <div class="text-muted small">
                        Total: {{ $categories->count() }} category(ies)
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-success text-center">
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Instructions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                                <tr class="text-center">
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->description }}</td>
                                    <td>
                                        @if($category->recycling_instructions)
                                            {{ $category->recycling_instructions }}
                                        @else
                                            <span class="">Without Instructions</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- Edit -->
                                            <a href="{{ route('waste_categories.edit', $category->id) }}" 
                                               class="action-btn action-edit" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <!-- Delete -->
                                            <form action="{{ route('waste_categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
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
    @endif
</div>

<!-- Styles réutilisables depuis la page Points de Collecte -->
<style>
.page-title { font-weight: 600; font-size: 1.75rem; }
.card { border: none; transition: transform 0.2s ease-in-out; }
.card:hover { transform: translateY(-1px); }
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; }
.table th { border-bottom: 2px solid #198754; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; padding: 1rem 0.75rem; }
.table tbody tr:hover { background-color: rgba(25, 135, 84, 0.04); box-shadow: inset 0 0 0 1px rgba(25, 135, 84, 0.1); }
.action-btn { display:inline-flex; align-items:center; justify-content:center; width:2.5rem; height:2.5rem; border-radius:8px; border:none; text-decoration:none; transition:all 0.3s ease; position:relative; overflow:hidden; color:#fff!important; }
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

/* Gradient backgrounds for cards */
.bg-gradient-primary {
    background: linear-gradient(135deg, #f8e3a3ff, #edd58bff) !important;
}

.bg-gradient-success {
    background: linear-gradient(135deg, #f8e3a3ff, #edd58bff) !important;
}

.bg-gradient-warning {
    background: linear-gradient(135deg,  #f8e3a3ff, #edd58bff) !important;
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
</style>
@endsection