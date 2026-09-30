@extends('front.layout')

@section('content')
<div class="container py-5">
    <h1 class="mb-4 text-center text-success">Waste List</h1>

    @if($wastes->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-trash"></i>
            </div>
            <h3 class="mb-3">No wastes available</h3>
            <p class="text-muted mb-4">There are currently no waste items available.</p>
        </div>
    @else
        <div class="row">
            @foreach($wastes as $waste)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="waste-card card h-100 shadow">
                        <!-- Waste Image -->
                        @if($waste->image_path)
                        <div class="text-center p-3">
                            <img src="{{ asset('storage/' . $waste->image_path) }}" 
                                 alt="{{ $waste->type }}" 
                                 class="waste-image img-fluid rounded"
                                 style="height: 150px; object-fit: cover; width: 100%;">
                        </div>
                        @else
                        <div class="text-center p-3">
                            <div class="no-image bg-light rounded d-flex align-items-center justify-content-center"
                                 style="height: 150px;">
                                <i class="fas fa-trash text-muted" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                        @endif

                        <div class="card-body d-flex flex-column">
                            <!-- Category Only -->
                            <div class="category-section text-center mb-3">
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-folder me-1"></i>{{ $waste->category->name ?? 'N/A' }}
                                </span>
                            </div>

                            <!-- Show Details Button -->
                            <div class="mt-auto">
                                <button class="btn btn-outline-success w-100" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#wasteModal{{ $waste->id }}">
                                    <i class="fas fa-eye me-2"></i>View Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal for Waste Details -->
                <div class="modal fade" id="wasteModal{{ $waste->id }}" tabindex="-1" 
                     aria-labelledby="wasteModalLabel{{ $waste->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title" id="wasteModalLabel{{ $waste->id }}">
                                    <i class="fas fa-trash me-2"></i>{{ $waste->type }} - Complete Details
                                </h5>
                                <button type="button" class="btn-close btn-close-white" 
                                        data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <!-- Waste Image in Modal -->
                                    @if($waste->image_path)
                                    <div class="col-md-6 mb-4">
                                        <div class="text-center">
                                            <img src="{{ asset('storage/' . $waste->image_path) }}" 
                                                 alt="{{ $waste->type }}" 
                                                 class="img-fluid rounded shadow"
                                                 style="max-height: 300px; object-fit: cover; width: 100%;">
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <div class="{{ $waste->image_path ? 'col-md-6' : 'col-md-12' }}">
                                        <!-- Basic Information -->
                                        <div class="info-section mb-4">
                                            <h6 class="text-success mb-3">
                                                <i class="fas fa-info-circle me-2"></i>Basic Information
                                            </h6>
                                            <div class="row">
                                                <div class="col-6 mb-2 text-muted">
                                                    <strong>Type:</strong>
                                                </div>
                                                <div class="col-6 mb-2 text-dark">
                                                    {{ $waste->type }}
                                                </div>
                                                
                                                <div class="col-6 mb-2 text-muted">
                                                    <strong>Weight:</strong>
                                                </div>
                                                <div class="col-6 mb-2 text-dark">
                                                    {{ $waste->weight }} kg
                                                </div>
                                                
                                                <div class="col-6 mb-2 text-muted">
                                                    <strong>Status:</strong>
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <span class="badge status-{{ strtolower($waste->status) }}">
                                                        {{ $waste->status }}
                                                    </span>
                                                </div>
                                                
                                                <div class="col-6 mb-2 text-muted">
                                                    <strong>Category:</strong>
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <span class="badge bg-success">
                                                        {{ $waste->category->name ?? 'N/A' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Description -->
                                        @if($waste->description)
                                        <div class="info-section mb-4">
                                            <h6 class="text-success mb-3">
                                                <i class="fas fa-align-left me-2"></i>Description
                                            </h6>
                                            <p class="mb-0 text-dark">{{ $waste->description }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-2"></i>Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
    .waste-card {
        border: none;
        border-radius: 12px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }

    .waste-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }

    .waste-image {
        transition: transform 0.3s ease;
        border-radius: 8px;
    }

    .waste-card:hover .waste-image {
        transform: scale(1.05);
    }

    .category-section .badge {
        font-size: 0.9rem;
        padding: 8px 12px;
        border-radius: 20px;
    }

    .no-image {
        border: 2px dashed #dee2e6;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }

    .empty-state-icon {
        font-size: 4rem;
        color: #9ca3af;
        margin-bottom: 20px;
    }

    .info-section {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #198754;
        margin-bottom: 15px;
    }

    .modal-header {
        border-radius: 12px 12px 0 0;
    }

    .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }

    .text-dark {
        color: #212529 !important;
        font-weight: 500;
    }

    /* Status badges for modal - COULEURS CORRIGÉES */
    .badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status-pending {
        background-color: #fff3cd !important;
        color: #856404 !important;
        border: 1px solid #ffeaa7;
    }

    .status-processed {
        background-color: #d1ecf1 !important;
        color: #0c5460 !important;
        border: 1px solid #bee5eb;
    }

    .status-recycled {
        background-color: #d4edda !important;
        color: #155724 !important;
        border: 1px solid #c3e6cb;
    }

    .status-disposed {
        background-color: #f8d7da !important;
        color: #721c24 !important;
        border: 1px solid #f5c6cb;
    }

    .status-collected {
        background-color: #e2e3e5 !important;
        color: #176fb6ff !important;
        border: 1px solid #d6d8db;
    }

    .status-available {
        background-color: #d1ecf1 !important;
        color: #0c5460 !important;
        border: 1px solid #bee5eb;
    }

    /* Fallback pour les statuts non définis */
    [class*="status-"] {
        background-color: #447aa8ff !important;
        color: white !important;
        border: 1px solid #608badff;
    }

    .btn-outline-success {
        border: 2px solid #198754;
        color: #198754;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-outline-success:hover {
        background-color: #198754;
        color: white;
        transform: translateY(-2px);
    }
</style>

<!-- Bootstrap JS for Modals -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Optional: Add some interactive features -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add loading animation to images
    const wasteImages = document.querySelectorAll('.waste-image');
    wasteImages.forEach(img => {
        img.addEventListener('load', function() {
            this.style.opacity = '1';
        });
        img.style.opacity = '0';
        img.style.transition = 'opacity 0.3s ease';
    });

    // Add click animation to buttons
    const detailButtons = document.querySelectorAll('.btn-outline-success');
    detailButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });

    // Debug: Log all status classes to console
    const statusBadges = document.querySelectorAll('[class*="status-"]');
    statusBadges.forEach(badge => {
        console.log('Status badge:', badge.className, 'Text:', badge.textContent);
    });
</script>
@endsection