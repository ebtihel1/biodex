@extends('back.layout')

@section('title', 'User Management')

@section('content')
<style>
/* ===== Statistics ===== */
.user-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stats-wrapper {
    background: linear-gradient(135deg, #a8e6cf, #6fcf97);
    border-radius: 25px;
    padding: 2rem;
    color: #1b4332;
    margin-bottom: 2.5rem;
}

.stats-wrapper .stat-card {
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 20px;
    padding: 1.8rem;
    backdrop-filter: blur(12px);
    box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
    color: #1b4332;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}
.stats-wrapper .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}
.stats-wrapper .stat-icon { font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.9; }
.stats-wrapper .stat-number { font-size: 2rem; font-weight: 700; }
.stats-wrapper .stat-label { opacity: 0.95; font-size: 0.95rem; letter-spacing: 0.5px; }

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

/* ===== Table ===== */
.users-table { background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 6px 30px rgba(0,0,0,0.06); }
.users-table table { margin-bottom: 0; }
.users-table thead th {
    background: #f8fdf9; color: #1b4332; font-weight: 600;
    border-bottom: 2px solid #e1f3e4; white-space: nowrap; vertical-align: middle;
}
.users-table thead th a { color: inherit; text-decoration: none; }
.users-table tbody td { vertical-align: middle; }
.user-avatar-initial {
    display: inline-grid; width: 40px; height: 40px; place-items: center;
    border-radius: 50%; background: #e1f3e4; color: #17634d;
    font-weight: 700; overflow: hidden;
}
.user-avatar-initial img { width: 100%; height: 100%; object-fit: cover; }

/* ===== Pagination ===== */
.pagination { margin-bottom: 0; }
.pagination .page-link { color: #198754; }
.pagination .page-item.active .page-link {
    background-color: #198754; border-color: #198754; color: #fff;
}

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

    {{-- Statistics --}}
    <div class="stats-wrapper fade-in-up">
        <div class="user-stats">
            <div class="stat-card">
                <i class="bi bi-people stat-icon"></i>
                <div class="stat-number">{{ number_format((int) $summary->total_count) }}</div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-shield-lock stat-icon"></i>
                <div class="stat-number">{{ number_format((int) $summary->admin_count) }}</div>
                <div class="stat-label">Administrators</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-building stat-icon"></i>
                <div class="stat-number">{{ number_format((int) $summary->company_count) }}</div>
                <div class="stat-label">Companies</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-person stat-icon"></i>
                <div class="stat-number">{{ number_format((int) $summary->citizen_count) }}</div>
                <div class="stat-label">Citizens</div>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <div class="page-header">
        <div>
            <h2 class="fw-bold text-success mb-1">
                <i class="bi bi-people me-2"></i>User Management
            </h2>
            <p class="text-muted mb-0">Manage platform users and their roles</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('back.users.create') }}" class="btn btn-add">
                <i class="bi bi-person-plus-fill me-2"></i>New User
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('back.users.index') }}" class="filter-bar">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label mb-1 small text-muted">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control" placeholder="Name, email, phone, city...">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1 small text-muted">Role</label>
                <select name="role" class="form-select">
                    <option value="">All roles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected((string) request('role') === (string) $role->id)>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1 small text-muted">Sort by</label>
                <select name="sort" class="form-select">
                    <option value="id"         @selected(request('sort') === 'id')>ID</option>
                    <option value="name"       @selected(request('sort') === 'name')>Name</option>
                    <option value="email"      @selected(request('sort') === 'email')>Email</option>
                    <option value="role_id"    @selected(request('sort') === 'role_id')>Role</option>
                    <option value="city"       @selected(request('sort') === 'city')>City</option>
                    <option value="created_at" @selected(request('sort') === 'created_at')>Created</option>
                </select>
            </div>
            <div class="col-6 col-md-1">
                <label class="form-label mb-1 small text-muted">Dir.</label>
                <select name="direction" class="form-select">
                    <option value="asc"  @selected(request('direction') !== 'desc')>↑</option>
                    <option value="desc" @selected(request('direction') === 'desc')>↓</option>
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-success flex-fill">
                    <i class="bi bi-funnel-fill me-1"></i>Filter
                </button>
                <a href="{{ route('back.users.index') }}" class="btn btn-outline-secondary" title="Reset filters">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </div>
    </form>

    @if ($users->isEmpty())
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                <h4 class="text-muted mb-3">No users found</h4>
                <p class="text-muted mb-4">
                    {{ request('search') || request('role')
                        ? 'Try changing or clearing your filters.'
                        : 'Start by adding your first user.' }}
                </p>
                @if(!request('search') && !request('role'))
                    <a href="{{ route('back.users.create') }}" class="btn btn-success px-4">
                        <i class="bi bi-person-plus me-2"></i> Create User
                    </a>
                @else
                    <a href="{{ route('back.users.index') }}" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-x-circle me-2"></i> Clear Filters
                    </a>
                @endif
            </div>
        </div>
    @else
        <div class="users-table table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Phone</th>
                        <th>City</th>
                        <th>Joined</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="user-avatar-initial">
                                        @if ($user->avatar)
                                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="">
                                        @else
                                            {{ mb_substr($user->name, 0, 1) }}
                                        @endif
                                    </span>
                                    <span class="fw-semibold">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @php
                                    $roleName = optional($user->role)->name ?? 'none';
                                    $roleBadge = match ($roleName) {
                                        'admin' => 'bg-danger',
                                        'entreprise' => 'bg-primary',
                                        'citoyen' => 'bg-success',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $roleBadge }}">{{ ucfirst($roleName) }}</span>
                            </td>
                            <td>{{ $user->phone ?: '—' }}</td>
                            <td>{{ $user->city ?: '—' }}</td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('back.users.show', $user->id) }}"
                                       class="btn btn-info btn-sm text-white" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('back.users.edit', $user->id) }}"
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('back.users.destroy', $user->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
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

        {{-- Pagination --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mt-4 gap-2">
            <div class="text-muted small">
                Showing {{ $users->firstItem() }}–{{ $users->lastItem() }}
                of {{ $users->total() }} entries
            </div>
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
