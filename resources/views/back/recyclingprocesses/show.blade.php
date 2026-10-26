{{-- resources/views/back/recyclingprocesses/show.blade.php --}}
@extends('back.layout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">

            {{-- Header --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <div>
                    <h1 class="h3 fw-bold text-success mb-1">
                        <i class="bi bi-recycle me-1"></i>
                        Recycling Process
                    </h1>
                    <p class="text-muted small mb-0">
                        Created on {{ optional($recyclingProcess->created_at)->format('M d, Y H:i') ?? '—' }}
                    </p>
                </div>
                <div>
                    @php
                        $statusClasses = [
                            'completed'   => 'bg-success',
                            'in_progress' => 'bg-warning text-dark',
                            'pending'     => 'bg-info text-dark',
                        ];
                        $statusClass = $statusClasses[$recyclingProcess->status] ?? 'bg-danger';
                    @endphp
                    <span class="badge {{ $statusClass }} fs-6 px-3 py-2">
                        {{ ucfirst(str_replace('_', ' ', $recyclingProcess->status ?? 'unknown')) }}
                    </span>
                </div>
            </div>

            {{-- Alerts --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="bi bi-exclamation-triangle me-1"></i>Errors:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-body p-3 p-md-4">

                    {{-- Section: General Information --}}
                    <h2 class="h6 text-uppercase text-muted fw-bold mb-3 border-bottom pb-2">
                        <i class="bi bi-info-circle me-1"></i>General Information
                    </h2>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Waste</label>
                            <input type="text" class="form-control" value="{{ optional($recyclingProcess->waste)->type ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Waste Category</label>
                            <input type="text" class="form-control" value="{{ optional(optional($recyclingProcess->waste)->category)->name ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Method</label>
                            <input type="text" class="form-control" value="{{ $recyclingProcess->method ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Responsible User</label>
                            <input type="text" class="form-control" value="{{ optional($recyclingProcess->responsibleUser)->name ?? 'N/A' }}" readonly>
                        </div>
                    </div>

                    {{-- Section: Process Details --}}
                    <h2 class="h6 text-uppercase text-muted fw-bold mb-3 border-bottom pb-2">
                        <i class="bi bi-gear me-1"></i>Process Details
                    </h2>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="text" class="form-control" value="{{ optional($recyclingProcess->start_date)->format('M d, Y') ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">End Date</label>
                            <input type="text" class="form-control" value="{{ optional($recyclingProcess->end_date)->format('M d, Y') ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Output Quantity (kg)</label>
                            <input type="text" class="form-control" value="{{ $recyclingProcess->output_quantity ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Output Quality</label>
                            <input type="text" class="form-control" value="{{ $recyclingProcess->output_quality ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea class="form-control" rows="4" readonly>{{ $recyclingProcess->notes ?: 'No notes provided.' }}</textarea>
                        </div>
                    </div>

                    {{-- Section: Metadata --}}
                    <h2 class="h6 text-uppercase text-muted fw-bold mb-3 border-bottom pb-2">
                        <i class="bi bi-clock-history me-1"></i>Metadata
                    </h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Created At</label>
                            <input type="text" class="form-control" value="{{ optional($recyclingProcess->created_at)->format('M d, Y H:i') ?? 'Unknown' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Last Updated</label>
                            <input type="text" class="form-control" value="{{ optional($recyclingProcess->updated_at)->format('M d, Y H:i') ?? 'Unknown' }}" readonly>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('recyclingprocesses.index') }}" class="btn btn-outline-secondary w-100 w-md-auto">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                        <div class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto justify-content-md-end">
                            <a href="{{ route('recyclingprocesses.edit', $recyclingProcess->id) }}" class="btn btn-warning w-100 w-md-auto">
                                <i class="bi bi-pencil-square me-1"></i>Edit
                            </a>
                            <form action="{{ route('recyclingprocesses.destroy', $recyclingProcess->id) }}" method="POST" class="w-100 w-md-auto"
                                  onsubmit="return confirm('Are you sure you want to delete this recycling process?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="bi bi-trash me-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection