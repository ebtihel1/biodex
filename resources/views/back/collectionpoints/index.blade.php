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

.page-header select {
    border-radius: 10px;
    border: 1px solid #dee2e6;
    padding: 0.5rem 1rem;
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

/* DataTables Enhancements */
.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
    border-radius: .25rem;
    border: 1px solid #ced4da;
    padding: .375rem .75rem;
    color: #198754;
}

.dataTables_wrapper .dataTables_length select:focus,
.dataTables_wrapper .dataTables_filter input:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: .25rem;
    margin: 0 2px;
    padding: .25rem .5rem;
    border: 1px solid #dee2e6;
    background: white;
    color: #198754 !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #198754 !important;
    color: white !important;
    border-color: #198754 !important;
}

.dataTables_wrapper .dataTables_info {
    color: #6c757d;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    margin-bottom: 1rem;
}

.dataTables_wrapper .dataTables_filter {
    text-align: right;
}

@media (max-width: 768px) {
    .dataTables_wrapper .dataTables_filter {
        text-align: left;
        margin-top: 1rem;
    }
}
</style>

<div class="container-fluid py-4 fade-in-up">
    <!-- Statistics -->
    <div class="stats-wrapper fade-in-up">
        <div class="collection-stats">
            <div class="stat-card bg-gradient-primary">
                <i class="bi bi-recycle stat-icon"></i>
                <div class="stat-number" id="total-points">{{ $collectionPoints->count() }}</div>
                <div class="stat-label">Total Collection Points</div>
            </div>
            <div class="stat-card bg-gradient-success">
                <i class="bi bi-check-circle-fill stat-icon"></i>
                <div class="stat-number" id="active-points">{{ $collectionPoints->where('status', 'active')->count() }}</div>
                <div class="stat-label">Active Points</div>
            </div>
            <div class="stat-card bg-gradient-warning">
                <i class="bi bi-pause-circle stat-icon"></i>
                <div class="stat-number" id="inactive-points">{{ $collectionPoints->where('status', 'inactive')->count() }}</div>
                <div class="stat-label">Inactive Points</div>
            </div>
            <div class="stat-card bg-gradient-secondary">
                <i class="bi bi-geo-alt stat-icon"></i>
                <div class="stat-number" id="cities-count">{{ $collectionPoints->pluck('city')->unique()->filter()->count() }}</div>
                <div class="stat-label">Cities Covered</div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-success mb-0">
            <i class="bi bi-recycle me-2"></i>Collection Points Management
        </h2>
        <div class="d-flex gap-2">
            <select id="status-filter" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="undefined">Undefined</option>
            </select>

            <a href="{{ route('collectionpoints.create') }}" class="btn btn-success btn-custom">
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
                            <th><i class="bi bi-hash"></i> ID</th>
                            <th><i class="bi bi-tag"></i> Name</th>
                            <th><i class="bi bi-geo-alt"></i> Address</th>
                            <th><i class="bi bi-building"></i> City</th>
                            <th><i class="bi bi-mailbox"></i> Postal Code</th>
                            <th><i class="bi bi-clock"></i> Opening Hours</th>
                            <th><i class="bi bi-tags"></i> Categories</th>
                            <th><i class="bi bi-telephone"></i> Contact</th>
                            <th><i class="bi bi-flag"></i> Status</th>
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
    @endif
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
$(document).ready(function () {
    let table = $('#collectionpoints-table').DataTable({
        responsive: true,
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/en-GB.json' },
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        columnDefs: [
            { responsivePriority: 1, targets: 1 }, // Name
            { responsivePriority: 2, targets: 9 }, // Actions
            { responsivePriority: 3, targets: 3 }, // City
            { responsivePriority: 4, targets: 8 }, // Status
        ]
    });

    // Status Filter
    $('#status-filter').on('change', function () {
        let val = $(this).val();
        if (val === 'undefined') {
            table.column(8).search('').draw();
            // We'll need to handle undefined status differently
            // This is a simplified approach
        } else {
            table.column(8).search(val).draw();
        }
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
@endsection