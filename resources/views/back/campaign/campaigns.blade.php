@extends('back.layout')

@section('title', 'Campaign Management')

@section('content')
<style>
/* ===== Statistics (uniformisé) ===== */
.campaign-stats {
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

/* ===== Fade in ===== */
.fade-in-up { animation: fadeInUp 0.6s ease-out; }
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(25px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ===== DataTables Enhancements (Green Theme) ===== */
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
.dataTables_wrapper .dataTables_info { color: #6c757d; }
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter { margin-bottom: 1rem; }
.dataTables_wrapper .dataTables_filter { text-align: right; }

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

.action-edit   { background: linear-gradient(135deg,#ffc107,#e0a800); }
.action-edit:hover { background: linear-gradient(135deg,#e0a800,#c69500); }
.action-delete { background: linear-gradient(135deg,#dc3545,#c82333); }
.action-delete:hover { background: linear-gradient(135deg,#c82333,#a71e2a); }

@media (max-width: 768px) {
    .stats-wrapper { padding: 1rem; }
    .btn-add { width: 100%; margin-top: 1rem; }
    .dataTables_wrapper .dataTables_filter {
        text-align: left;
        margin-top: 1rem;
    }
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
        <div class="campaign-stats">
            <div class="stat-card">
                <i class="bi bi-collection-fill stat-icon"></i>
                <div class="stat-number" id="total-campaigns">0</div>
                <div class="stat-label">Total Campaigns</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-lightning-charge-fill stat-icon"></i>
                <div class="stat-number" id="active-campaigns">0</div>
                <div class="stat-label">Active Campaigns</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-pencil-square stat-icon"></i>
                <div class="stat-number" id="draft-campaigns">0</div>
                <div class="stat-label">Drafts</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-archive stat-icon"></i>
                <div class="stat-number" id="closed-campaigns">0</div>
                <div class="stat-label">Closed</div>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <div class="page-header">
        <div>
            <h2 class="fw-bold text-success mb-1">
                <i class="bi bi-megaphone-fill me-2"></i>Campaign Management
            </h2>
            <p class="text-muted mb-0">Create, filter and manage campaigns</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('campaigns.export.csv') }}" class="btn btn-outline-success">
                <i class="bi bi-filetype-csv me-1"></i>Export CSV
            </a>
            <a href="{{ route('campaigns.export.pdf') }}" class="btn btn-outline-danger">
                <i class="bi bi-filetype-pdf me-1"></i>Export PDF
            </a>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-upload me-1"></i>Import CSV
            </button>
            <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#createCampaignModal">
                <i class="bi bi-plus-circle-fill me-2"></i>New Campaign
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-card">
        <div class="card-body p-0">
            <table id="campaigns-table" class="table table-hover align-middle w-100 mb-0">
                <thead class="table-light">
                    <tr class="text-center">
                        <th><i class="bi bi-hash"></i> ID</th>
                        <th><i class="bi bi-tag"></i> Title</th>
                        <th><i class="bi bi-text-paragraph"></i> Description</th>
                        <th><i class="bi bi-calendar-range"></i> Dates</th>
                        <th><i class="bi bi-flag"></i> Status</th>
                        <th><i class="bi bi-person"></i> User</th>
                        <th><i class="bi bi-gear"></i> Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal: Creation --}}
@include('back.campaign.create')

{{-- Modal: Edit --}}
@include('back.campaign.edit')

{{-- Modal: Import --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('campaigns.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="bi bi-upload me-2"></i>Import Campaigns
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
                        Headers may contain (title, description, image, start_date, end_date, status, user_id).
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

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
$(document).ready(function () {
    let table = $('#campaigns-table').DataTable({
        responsive: true,
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/en-GB.json' },
        ajax: { url: '{{ url("campaigns") }}', dataSrc: '' },
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [[0, 'desc']],
        columns: [
            { data: 'id' },
            { data: 'title', render: data => `<strong>${data}</strong>` },
            { data: 'description', defaultContent: '' },
            { data: null, render: data => `${data.start_date ?? '—'} → ${data.end_date ?? '—'}` },
            {
                data: 'status',
                render: function(data, type, row) {
                    const map = {
                        draft:  ['warning',   'Draft'],
                        active: ['success',   'Active'],
                        closed: ['secondary', 'Closed']
                    };
                    if(type === 'display') {
                        const cfg = map[data] ?? ['secondary', data];
                        return `<span class="badge bg-${cfg[0]} bg-opacity-15 text-${cfg[0]} border border-${cfg[0]} border-opacity-25">${cfg[1]}</span>`;
                    }
                    return data;
                }
            },
            { data: 'user', render: data => data ? data.name : 'N/A' },
            {
                data: null,
                orderable: false,
                render: data => `
                    <div class="action-buttons">
                        <button class="action-btn action-edit edit-btn" data-id="${data.id}" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="action-btn action-delete delete-btn" data-id="${data.id}" title="Delete">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>`
            }
        ]
    });

    // === Statistics ===
    function updateStats() {
        $.get('{{ url("campaigns") }}', data => {
            $('#total-campaigns').text(data.length);
            $('#active-campaigns').text(data.filter(c => c.status === 'active').length);
            $('#draft-campaigns').text(data.filter(c => c.status === 'draft').length);
            $('#closed-campaigns').text(data.filter(c => c.status === 'closed').length);
        });
    }
    updateStats();

    // === Creation ===
    $('#createCampaignForm').on('submit', e => {
        e.preventDefault();
        $.post('{{ url("campaigns") }}', $('#createCampaignForm').serialize())
            .done(() => {
                $('#createCampaignModal').modal('hide');
                $('#createCampaignForm')[0].reset();
                table.ajax.reload();
                updateStats();
            }).fail(xhr => alert('Creation Error: ' + xhr.responseText));
    });

    // === Edit — open modal ===
    $(document).on('click', '.edit-btn', function () {
        let id = $(this).data('id');
        $.get(`{{ url("campaigns") }}/${id}`, function(campaign) {
            $('#editCampaignId').val(campaign.id);
            $('#editTitle').val(campaign.title);
            $('#editDescription').val(campaign.description);
            $('#editStartDate').val(campaign.start_date);
            $('#editEndDate').val(campaign.end_date);
            $('#editStatus').val(campaign.status);
            $('#editCampaignModal').modal('show');
        }).fail(xhr => alert('Error retrieving campaign: ' + xhr.responseText));
    });

    // === Edit — submit ===
    $('#editCampaignForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#editCampaignId').val();

        $.ajax({
            url: `{{ url("campaigns") }}/${id}`,
            type: 'PUT',
            data: $(this).serialize(),
            success: function() {
                $('#editCampaignModal').modal('hide');
                table.ajax.reload();
                updateStats();
            },
            error: function(xhr) {
                alert('Update Error: ' + xhr.responseText);
            }
        });
    });

    // === Deletion ===
    $(document).on('click', '.delete-btn', function () {
        if (confirm('Confirm deletion?')) {
            $.ajax({
                url: `{{ url("campaigns") }}/${$(this).data('id')}`,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: () => {
                    table.ajax.reload();
                    updateStats();
                },
                error: xhr => alert('Deletion Error: ' + xhr.responseText)
            });
        }
    });

    // === Auto-open import modal on validation error ===
    @if ($errors->has('csv_file'))
        new bootstrap.Modal(document.getElementById('importModal')).show();
    @endif
});
</script>
@endsection