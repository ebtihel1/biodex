@extends('back.layout')

@section('title', 'Edit Collection Point')

@section('content')
<style>
    :root {
        --primary-green: #198754;
        --light-green: #d1e7dd;
        --dark-green: #0f5132;
        --success-gradient: linear-gradient(135deg, #198754 0%, #20c997 100%);
        --card-shadow: 0 1rem 3rem rgba(25, 135, 84, 0.15);
        --hover-shadow: 0 0.5rem 1rem rgba(25, 135, 84, 0.2);
    }
    
    .page-container {
        background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%);
        min-height: 100vh;
        padding: 1rem 0;
    }
    
    .card {
        border: none;
        box-shadow: var(--card-shadow);
        border-radius: 20px;
        overflow: hidden;
        background: white;
        transition: all 0.3s ease;
    }
    
    .card-header {
        background: var(--success-gradient) !important;
        border-bottom: none;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    
    .card-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent 0%, rgba(255,255,255,0.1) 100%);
    }
    
    .form-section {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .form-section:hover {
        box-shadow: var(--hover-shadow);
        border-color: var(--light-green);
    }
    
    .json-field {
        font-family: 'JetBrains Mono', 'Courier New', monospace;
        font-size: 0.9rem;
        border-radius: 12px;
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
        line-height: 1.4;
    }
    
    .json-field:focus {
        border-color: var(--primary-green);
        background: white;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.15);
    }
    
    .btn-action {
        min-width: 140px;
        padding: 0.875rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        font-size: 0.95rem;
    }
    
    .btn-success {
        background: var(--success-gradient);
        box-shadow: 0 0.25rem 0.5rem rgba(25, 135, 84, 0.3);
    }
    
    .btn-success:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(25, 135, 84, 0.4);
    }
    
    .btn-outline-success {
        border: 2px solid var(--primary-green);
        color: var(--primary-green);
        background: transparent;
    }
    
    .btn-outline-success:hover {
        background: var(--primary-green);
        transform: translateY(-2px);
        box-shadow: 0 0.25rem 0.5rem rgba(25, 135, 84, 0.3);
    }
    
    .form-control, .form-select {
        border-radius: 12px;
        padding: 1rem 1.25rem;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
        font-size: 1rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }
    
    .json-format-btn {
        border-radius: 0 12px 12px 0;
        border: 2px solid #e9ecef;
        border-left: none;
        background: white;
        transition: all 0.3s ease;
        color: var(--dark-green);
    }
    
    .json-format-btn:hover {
        background: var(--light-green);
        color: var(--dark-green);
    }
    
    .alert {
        border-radius: 16px;
        border: none;
        padding: 1.5rem;
    }
    
    .alert-danger {
        background: rgba(220, 53, 69, 0.1);
        border-left: 4px solid #dc3545;
    }
    
    .floating-label {
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .floating-label .form-control {
        padding-top: 1.75rem;
        height: auto;
    }
    
    .floating-label label {
        position: absolute;
        top: 0.75rem;
        left: 1.25rem;
        font-size: 0.9rem;
        color: #6c757d;
        transition: all 0.3s ease;
        pointer-events: none;
        background: white;
        padding: 0 0.5rem;
        margin-left: -0.5rem;
    }
    
    .floating-label .form-control:focus + label,
    .floating-label .form-control:not(:placeholder-shown) + label {
        top: -0.5rem;
        font-size: 0.8rem;
        color: var(--primary-green);
        font-weight: 600;
    }
    
    .icon-wrapper {
        width: 50px;
        height: 50px;
        border-radius: 16px;
        background: var(--light-green);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--dark-green);
        margin-right: 1rem;
        font-size: 1.25rem;
    }
    
    .field-hint {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .is-valid {
        border-color: var(--primary-green) !important;
    }
    
    .is-invalid {
        border-color: #dc3545 !important;
    }
    
    .form-section-header {
        border-bottom: 2px solid var(--light-green);
        padding-bottom: 1rem;
        margin-bottom: 2rem;
    }
    
    .action-buttons {
        border-top: 2px solid var(--light-green);
        padding-top: 2rem;
        margin-top: 1rem;
    }
    
    .json-example {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 0.5rem;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.8rem;
        color: #495057;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .json-example:hover {
        background: #e9ecef;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .page-container {
            padding: 0.5rem 0;
        }
        
        .card-header {
            padding: 1rem;
        }
        
        .form-section {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 16px;
        }
        
        .icon-wrapper {
            width: 40px;
            height: 40px;
            font-size: 1rem;
            margin-right: 0.75rem;
        }
        
        .btn-action {
            min-width: 120px;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }
        
        .form-control, .form-select {
            padding: 0.875rem 1rem;
            font-size: 0.95rem;
        }
        
        .floating-label label {
            left: 1rem;
            font-size: 0.85rem;
        }
        
        .floating-label .form-control:focus + label,
        .floating-label .form-control:not(:placeholder-shown) + label {
            top: -0.4rem;
            font-size: 0.75rem;
        }
        
        .json-field {
            font-size: 0.85rem;
        }
        
        .action-buttons {
            padding-top: 1.5rem;
        }
        
        .action-buttons .d-flex {
            flex-direction: column;
            gap: 1rem;
        }
        
        .action-buttons .d-flex .d-flex {
            flex-direction: row;
            justify-content: center;
            width: 100%;
        }
    }
    
    @media (max-width: 576px) {
        .page-container {
            padding: 0.25rem 0;
        }
        
        .card-header {
            padding: 0.875rem;
        }
        
        .form-section {
            padding: 0.875rem;
            border-radius: 12px;
        }
        
        .icon-wrapper {
            width: 35px;
            height: 35px;
            font-size: 0.9rem;
            margin-right: 0.5rem;
        }
        
        .btn-action {
            min-width: auto;
            padding: 0.675rem 0.875rem;
            font-size: 0.85rem;
        }
        
        .form-control, .form-select {
            padding: 0.75rem 0.875rem;
            font-size: 0.9rem;
        }
        
        .json-field {
            font-size: 0.8rem;
        }
        
        .form-section-header .d-flex {
            flex-direction: column;
            text-align: center;
        }
        
        .form-section-header .icon-wrapper {
            margin: 0 auto 0.5rem;
        }
        
        .json-example {
            padding: 0.75rem;
            font-size: 0.75rem;
        }
    }
</style>

<div class="page-container">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-xxl-10">
                
                @if($collectionPoint)
                <!-- Header Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h1 class="h2 text-white mb-2">
                                    <i class="bi bi-pencil-square me-2"></i>Edit Collection Point
                                </h1>
                                <p class="text-white-50 mb-0">Update the details for {{ $collectionPoint->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Form -->
                <form action="{{ route('collectionpoints.update', ['collectionpoint' => $collectionPoint->id]) }}" method="POST" id="editForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Display Validation Errors -->
                    @if($errors->any())
                        <div class="alert alert-danger mb-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                                <div>
                                    <h5 class="alert-heading mb-2">Please fix the following errors:</h5>
                                    <ul class="mb-0 ps-3">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Location Information Section -->
                    <div class="form-section">
                        <div class="form-section-header">
                            <div class="d-flex align-items-center">
                                <div class="icon-wrapper">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div>
                                    <h4 class="text-success mb-1">Location Information</h4>
                                    <p class="text-muted mb-0">Basic location details of the collection point</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="floating-label">
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $collectionPoint->name) }}" 
                                           placeholder=" " required>
                                    <label for="name">Collection Point Name *</label>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="floating-label">
                                    <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" 
                                           value="{{ old('city', $collectionPoint->city) }}" 
                                           placeholder=" " required>
                                    <label for="city">City *</label>
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="floating-label">
                                    <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror" 
                                           value="{{ old('address', $collectionPoint->address) }}" 
                                           placeholder=" " required>
                                    <label for="address">Full Address *</label>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="floating-label">
                                    <input type="text" name="postal_code" id="postal_code" class="form-control @error('postal_code') is-invalid @enderror" 
                                           value="{{ old('postal_code', $collectionPoint->postal_code) }}" 
                                           placeholder=" ">
                                    <label for="postal_code">Postal Code</label>
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="floating-label">
                                    <input type="number" step="any" name="latitude" id="latitude" class="form-control @error('latitude') is-invalid @enderror" 
                                           value="{{ old('latitude', $collectionPoint->latitude) }}" 
                                           placeholder=" ">
                                    <label for="latitude">Latitude</label>
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="floating-label">
                                    <input type="number" step="any" name="longitude" id="longitude" class="form-control @error('longitude') is-invalid @enderror" 
                                           value="{{ old('longitude', $collectionPoint->longitude) }}" 
                                           placeholder=" ">
                                    <label for="longitude">Longitude</label>
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact & Status Section -->
                    <div class="form-section">
                        <div class="form-section-header">
                            <div class="d-flex align-items-center">
                                <div class="icon-wrapper">
                                    <i class="bi bi-telephone"></i>
                                </div>
                                <div>
                                    <h4 class="text-success mb-1">Contact & Status</h4>
                                    <p class="text-muted mb-0">Contact information and operational status</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="floating-label">
                                    <input type="text" name="contact_phone" id="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror" 
                                           value="{{ old('contact_phone', $collectionPoint->contact_phone) }}" 
                                           placeholder=" ">
                                    <label for="contact_phone">Phone Number</label>
                                    @error('contact_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="floating-label">
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="active" {{ old('status', $collectionPoint->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $collectionPoint->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <label for="status">Point Status *</label>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Advanced Configuration Section -->
                    <div class="form-section">
                        <div class="form-section-header">
                            <div class="d-flex align-items-center">
                                <div class="icon-wrapper">
                                    <i class="bi bi-gear"></i>
                                </div>
                                <div>
                                    <h4 class="text-success mb-1">Advanced Configuration</h4>
                                    <p class="text-muted mb-0">Additional settings and configurations</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Opening Hours -->
                        <div class="mb-4">
                            <label for="opening_hours" class="form-label fw-semibold text-success mb-3">
                                <i class="bi bi-clock-history me-2"></i>Opening Hours
                            </label>
                            <div class="input-group">
                                <textarea name="opening_hours" id="opening_hours" class="form-control json-field @error('opening_hours') is-invalid @enderror" 
                                          rows="2" placeholder="Enter opening hours in JSON format..."></textarea>
                                <button type="button" class="btn json-format-btn" data-bs-toggle="tooltip" title="Format JSON">
                                    <i class="bi bi-code-slash"></i>
                                </button>
                            </div>
                            @error('opening_hours')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Accepted Categories -->
                        <div class="mb-3">
                            <label for="accepted_categories" class="form-label fw-semibold text-success mb-3">
                                <i class="bi bi-folder-check me-2"></i>Accepted Categories
                            </label>
                            <div class="input-group">
                                <textarea name="accepted_categories" id="accepted_categories" class="form-control json-field @error('accepted_categories') is-invalid @enderror" 
                                          rows="2" placeholder="Enter accepted categories in JSON format..."></textarea>
                                <button type="button" class="btn json-format-btn" data-bs-toggle="tooltip" title="Format JSON">
                                    <i class="bi bi-code-slash"></i>
                                </button>
                            </div>
                            @error('accepted_categories')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="action-buttons">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('collectionpoints.index') }}" class="btn btn-outline-success btn-action">
                                <i class="bi bi-arrow-left me-2"></i> Back to List
                            </a>
                            <div class="d-flex gap-3">
                                <button type="reset" class="btn btn-light btn-action">
                                    <i class="bi bi-arrow-clockwise me-2"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-success btn-action">
                                    <i class="bi bi-check-lg me-2"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                @else
                    <div class="alert alert-danger d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill me-3 fs-3"></i>
                        <div>
                            <h5 class="alert-heading mb-1 fw-bold">Collection Point Not Found</h5>
                            <p class="mb-0">The collection point you're trying to edit doesn't exist.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // JSON formatting function
        document.querySelectorAll('.json-format-btn').forEach(button => {
            button.addEventListener('click', function() {
                const inputGroup = this.closest('.input-group');
                const textarea = inputGroup.querySelector('textarea');
                
                try {
                    if (textarea.value.trim()) {
                        const jsonObj = JSON.parse(textarea.value);
                        textarea.value = JSON.stringify(jsonObj, null, 2);
                        textarea.classList.remove('is-invalid');
                        textarea.classList.add('is-valid');
                    }
                } catch(e) {
                    textarea.classList.remove('is-valid');
                    textarea.classList.add('is-invalid');
                }
            });
        });

        // Form validation
        const form = document.getElementById('editForm');
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validate required fields
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                }
            });
            
            // Validate JSON fields
            const jsonFields = form.querySelectorAll('.json-field');
            jsonFields.forEach(field => {
                if (field.value.trim()) {
                    try {
                        JSON.parse(field.value);
                        field.classList.remove('is-invalid');
                    } catch(e) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection