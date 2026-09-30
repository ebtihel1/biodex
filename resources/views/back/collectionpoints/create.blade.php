@extends('back.layout')

@section('title', 'Add Collection Point')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xxl-10 col-xl-12 col-lg-12">
            
            <!-- Main Form Card -->
            <div class="card shadow-lg border-0">
                <div class="card-header bg-success text-white py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 fw-semibold">
                                <i class="bi bi-plus-circle me-2"></i>
                                New Collection Point
                            </h4>
                            <p class="mb-0 opacity-75">Fill in the information below</p>
                        </div>
                        <div class="badge bg-white bg-opacity-20 text-white px-3 py-2">
                            <i class="bi bi-stars me-1"></i>New
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Validation Errors</h6>
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li class="small">{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('collectionpoints.store') }}" method="POST" id="collectionPointForm">
                        @csrf

                        <!-- Step Navigation -->
                        <div class="form-steps mb-4">
                            <div class="steps-progress">
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 0%;"></div>
                                </div>
                                <div class="steps">
                                    <div class="step active" data-step="1">
                                        <div class="step-icon">1</div>
                                        <span class="step-label">Basic Info</span>
                                    </div>
                                    <div class="step" data-step="2">
                                        <div class="step-icon">2</div>
                                        <span class="step-label">Location</span>
                                    </div>
                                    <div class="step" data-step="3">
                                        <div class="step-icon">3</div>
                                        <span class="step-label">Configuration</span>
                                    </div>
                                    <div class="step" data-step="4">
                                        <div class="step-icon">4</div>
                                        <span class="step-label">Review</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 1: Basic Information -->
                        <div class="form-step active" data-step="1">
                            <div class="mb-4">
                                <h5 class="text-success mb-3">
                                    <i class="bi bi-info-circle me-2"></i>Basic Information
                                </h5>
                                <p class="text-muted mb-4">Main information about the collection point</p>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold">
                                        Point Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" class="form-control" 
                                           id="name" value="{{ old('name') }}" 
                                           placeholder="Enter collection point name" required>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1 text-success"></i>Official name of the collection point
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="city" class="form-label fw-semibold">
                                        City <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="city" class="form-control" 
                                           id="city" value="{{ old('city') }}" 
                                           placeholder="Enter city" required>
                                </div>
                                
                                <div class="col-12">
                                    <label for="address" class="form-label fw-semibold">
                                        Full Address <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="address" class="form-control" 
                                           id="address" value="{{ old('address') }}" 
                                           placeholder="Enter full address" required>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Location -->
                        <div class="form-step" data-step="2">
                            <div class="mb-4">
                                <h5 class="text-success mb-3">
                                    <i class="bi bi-geo-alt me-2"></i>Location Details
                                </h5>
                                <p class="text-muted mb-4">Precise location information</p>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="postal_code" class="form-label fw-semibold">Postal Code</label>
                                    <input type="text" name="postal_code" class="form-control" 
                                           id="postal_code" value="{{ old('postal_code') }}" 
                                           placeholder="e.g., 10001">
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="latitude" class="form-label fw-semibold">Latitude</label>
                                    <input type="number" step="any" name="latitude" class="form-control" 
                                           id="latitude" value="{{ old('latitude') }}" 
                                           placeholder="e.g., 40.7128">
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="longitude" class="form-label fw-semibold">Longitude</label>
                                    <input type="number" step="any" name="longitude" class="form-control" 
                                           id="longitude" value="{{ old('longitude') }}" 
                                           placeholder="e.g., -74.0060">
                                </div>
                            </div>

                            <div class="alert alert-info mt-4">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    <div>
                                        <small>Geographic coordinates are optional but recommended for map display.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Configuration -->
                        <div class="form-step" data-step="3">
                            <div class="mb-4">
                                <h5 class="text-success mb-3">
                                    <i class="bi bi-gear me-2"></i>Configuration
                                </h5>
                                <p class="text-muted mb-4">Settings and operational details</p>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="contact_phone" class="form-label fw-semibold">Phone Number</label>
                                    <input type="text" name="contact_phone" class="form-control" 
                                           id="contact_phone" value="{{ old('contact_phone') }}" 
                                           placeholder="e.g., +1 234 567 8900">
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-semibold">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <select name="status" class="form-select" id="status" required>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>🟢 Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>🔴 Inactive</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Opening Hours
                                       
                                    </label>
                                    <div class="input-group">
                                        <textarea name="opening_hours" class="form-control json-field" 
                                                  rows="1" >{{ old('opening_hours') }}</textarea>
                                        <button type="button" class="btn btn-outline-success json-format-btn">
                                            <i class="bi bi-code-slash"></i>
                                        </button>
                                    </div>
                                  
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Accepted Categories
                                      
                                    </label>
                                    <div class="input-group">
                                        <textarea name="accepted_categories" class="form-control json-field" 
                                                  rows="1" >{{ old('accepted_categories') }}</textarea>
                                        <button type="button" class="btn btn-outline-success json-format-btn">
                                            <i class="bi bi-code-slash"></i>
                                        </button>
                                    </div>
                                 
                                </div>
                            </div>


                        </div>

                        <!-- Step 4: Review -->
                        <div class="form-step" data-step="4">
                            <div class="mb-4">
                                <h5 class="text-success mb-3">
                                    <i class="bi bi-check-circle me-2"></i>Review Information
                                </h5>
                                <p class="text-muted mb-4">Verify all details before creating the collection point</p>
                            </div>

                            <div class="card border-0 bg-light mb-4">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <strong class="text-success">Name:</strong> 
                                            <span id="review-name" class="text-dark ms-2">-</span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong class="text-success">City:</strong> 
                                            <span id="review-city" class="text-dark ms-2">-</span>
                                        </div>
                                        <div class="col-12">
                                            <strong class="text-success">Address:</strong> 
                                            <span id="review-address" class="text-dark ms-2">-</span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong class="text-success">Status:</strong> 
                                            <span id="review-status" class="text-dark ms-2">-</span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong class="text-success">Phone:</strong> 
                                            <span id="review-phone" class="text-dark ms-2">-</span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong class="text-success">Postal Code:</strong> 
                                            <span id="review-postal" class="text-dark ms-2">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-success">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-shield-check me-2"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1">Ready to Create!</h6>
                                        <p class="mb-0 small">Make sure all information is correct before creating the collection point.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step Navigation -->
                        <div class="form-navigation mt-4 pt-4 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-outline-secondary btn-prev" disabled>
                                    <i class="bi bi-chevron-left me-2"></i>Previous
                                </button>
                                
                                <div class="text-muted small">
                                    Step <span class="current-step fw-bold">1</span> of <span class="total-steps fw-bold">4</span>
                                </div>
                                
                                <div>
                                    <button type="button" class="btn btn-success btn-next">
                                        Next <i class="bi bi-chevron-right ms-2"></i>
                                    </button>
                                    <button type="submit" class="btn btn-success btn-submit">
                                        <i class="bi bi-plus-circle me-2"></i>Create Point
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.page-header {
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 1rem;
}

.page-title {
    color: #2c3e50;
    font-weight: 700;
    font-size: 1.75rem;
}

.card {
    border: none;
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
}

.card-header {
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

/* Steps Navigation */
.form-steps {
    position: relative;
    margin-bottom: 2rem;
}

.steps-progress {
    position: relative;
}

.steps {
    display: flex;
    justify-content: space-between;
    position: relative;
    margin-top: -12px;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 2;
}

.step-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
    margin-bottom: 6px;
    transition: all 0.3s ease;
    border: 2px solid white;
}

.step.active .step-icon {
    background: #198754;
    color: white;
}

.step-label {
    font-size: 0.75rem;
    font-weight: 500;
    color: #6c757d;
}

.step.active .step-label {
    color: #198754;
    font-weight: 600;
}

/* Form Sections */
.form-step {
    display: none;
    animation: fadeIn 0.3s ease-in;
}

.form-step.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Form Elements */
.form-label {
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.75rem;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

.json-field {
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
    border-radius: 0.375rem 0 0 0.375rem;
    resize: vertical;
}

.json-format-btn {
    border-radius: 0 0.375rem 0.375rem 0;
}

/* Buttons */
.btn {
    border-radius: 0.375rem;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-success {
    background-color: #198754;
    border-color: #198754;
}

.btn-success:hover {
    background-color: #157347;
    border-color: #146c43;
    transform: translateY(-1px);
}

.btn-outline-success {
    color: #198754;
    border-color: #198754;
}

.btn-outline-success:hover {
    background-color: #198754;
    border-color: #198754;
    color: white;
}

.btn-submit {
    display: none;
}

/* Alerts */
.alert {
    border: none;
    border-radius: 0.5rem;
    border-left: 4px solid;
}

.alert-success {
    border-left-color: #198754;
    background-color: rgba(25, 135, 84, 0.1);
}

.alert-info {
    border-left-color: #0dcaf0;
    background-color: rgba(13, 202, 240, 0.1);
}

.alert-warning {
    border-left-color: #ffc107;
    background-color: rgba(255, 193, 7, 0.1);
}

/* Badges */
.badge {
    font-size: 0.65em;
    font-weight: 500;
}

/* Responsive */
@media (max-width: 768px) {
    .page-title {
        font-size: 1.5rem;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .form-navigation {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .form-navigation > div {
        width: 100%;
        justify-content: center !important;
    }
    
    .steps {
        gap: 0.5rem;
    }
    
    .step-label {
        font-size: 0.7rem;
    }
}

/* Error states */
.is-invalid {
    border-color: #dc3545 !important;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.shake {
    animation: shake 0.5s ease-in-out;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Form steps management
    const steps = document.querySelectorAll('.form-step');
    const stepButtons = {
        prev: document.querySelector('.btn-prev'),
        next: document.querySelector('.btn-next'),
        submit: document.querySelector('.btn-submit')
    };
    const stepIndicator = document.querySelector('.current-step');
    const progressBar = document.querySelector('.progress-bar');
    let currentStep = 1;
    
    // Show current step
    function showStep(step) {
        steps.forEach(s => s.classList.remove('active'));
        document.querySelector(`.form-step[data-step="${step}"]`).classList.add('active');
        
        // Update navigation
        stepButtons.prev.disabled = step === 1;
        stepButtons.next.style.display = step === steps.length ? 'none' : 'inline-block';
        stepButtons.submit.style.display = step === steps.length ? 'inline-block' : 'none';
        
        // Update indicator
        stepIndicator.textContent = step;
        
        // Update progress bar
        const progress = ((step - 1) / (steps.length - 1)) * 100;
        progressBar.style.width = `${progress}%`;
        
        // Update visual steps
        document.querySelectorAll('.step').forEach((s, index) => {
            if (index + 1 <= step) {
                s.classList.add('active');
            } else {
                s.classList.remove('active');
            }
        });
        
        // Update review at step 4
        if (step === 4) {
            updateReview();
        }
    }
    
    // Next navigation
    stepButtons.next.addEventListener('click', function() {
        if (validateStep(currentStep)) {
            currentStep++;
            showStep(currentStep);
        }
    });
    
    // Previous navigation
    stepButtons.prev.addEventListener('click', function() {
        currentStep--;
        showStep(currentStep);
    });
    
    // Step validation
    function validateStep(step) {
        const currentStepEl = document.querySelector(`.form-step[data-step="${step}"]`);
        const inputs = currentStepEl.querySelectorAll('[required]');
        let valid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                valid = false;
                input.classList.add('is-invalid');
                
                // Shake animation
                input.classList.add('shake');
                setTimeout(() => {
                    input.classList.remove('shake');
                }, 500);
            } else {
                input.classList.remove('is-invalid');
            }
        });
        
        if (!valid) {
            showAlert('Please fill all required fields', 'warning');
        }
        
        return valid;
    }
    
    // JSON formatting
    document.querySelectorAll('.json-format-btn').forEach(button => {
        button.addEventListener('click', function() {
            const inputGroup = this.closest('.input-group');
            const textarea = inputGroup.querySelector('textarea');
            
            try {
                if (textarea.value.trim()) {
                    const jsonObj = JSON.parse(textarea.value);
                    textarea.value = JSON.stringify(jsonObj, null, 2);
                    showAlert('JSON formatted successfully', 'success');
                }
            } catch(e) {
                showAlert('Invalid JSON: ' + e.message, 'danger');
            }
        });
    });
    
    // Review update
    function updateReview() {
        document.getElementById('review-name').textContent = document.getElementById('name').value || '-';
        document.getElementById('review-city').textContent = document.getElementById('city').value || '-';
        document.getElementById('review-address').textContent = document.getElementById('address').value || '-';
        document.getElementById('review-status').textContent = 
            document.getElementById('status').options[document.getElementById('status').selectedIndex].text;
        document.getElementById('review-phone').textContent = document.getElementById('contact_phone').value || '-';
        document.getElementById('review-postal').textContent = document.getElementById('postal_code').value || '-';
    }
    
    // Alert function
    function showAlert(message, type = 'info') {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="bi bi-${getAlertIcon(type)} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Remove existing alerts
        document.querySelectorAll('.alert-dismissible').forEach(alert => alert.remove());
        
        // Add new alert
        document.querySelector('.card-body').insertAdjacentHTML('afterbegin', alertHtml);
    }
    
    function getAlertIcon(type) {
        const icons = {
            'success': 'check-circle-fill',
            'danger': 'exclamation-triangle-fill',
            'warning': 'exclamation-triangle-fill',
            'info': 'info-circle-fill'
        };
        return icons[type] || 'info-circle-fill';
    }
    
    // Final form validation
    document.getElementById('collectionPointForm').addEventListener('submit', function(e) {
        if (!validateStep(currentStep)) {
            e.preventDefault();
            showAlert('Please correct errors before submitting', 'danger');
        }
    });
    
    // Remove invalid class when user starts typing
    document.querySelectorAll('input, select, textarea').forEach(element => {
        element.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });
    
    // Initialize
    showStep(currentStep);
});
</script>
@endsection