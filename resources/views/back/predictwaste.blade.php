@extends('back.layout')

@section('content')
<style>
.glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.gradient-header {
    background: linear-gradient(135deg, #00b09b, #96c93d);
    border-radius: 20px 20px 0 0;
    padding: 1.2rem 1rem !important;
}

.gradient-header .advice-icon {
    font-size: 2rem !important;
    margin-bottom: 0.5rem !important;
}

.gradient-header h1 {
    font-size: 1.75rem !important;
    margin-bottom: 0.4rem !important;
}

.gradient-header .lead {
    font-size: 0.875rem !important;
    margin-bottom: 0 !important;
}

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #00b09b;
    box-shadow: 0 0 0 3px rgba(0, 176, 155, 0.1);
    transform: translateY(-2px);
}

.form-label {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 8px;
}

.btn-ai {
    background: linear-gradient(135deg, #00b09b, #96c93d);
    border: none;
    border-radius: 50px;
    padding: 14px 40px;
    font-weight: 600;
    font-size: 18px;
    color: white;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(0, 176, 155, 0.3);
}

.btn-ai:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(0, 176, 155, 0.4);
    background: linear-gradient(135deg, #00a18b, #8bc34a);
}

.advice-card {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border: 2px solid #00b09b;
    border-radius: 16px;
    color: #2d3748;
    box-shadow: 0 10px 30px rgba(0, 176, 155, 0.15);
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
}

.advice-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, #00b09b, #96c93d);
}

.advice-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, #00b09b, #96c93d);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.advice-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #00b09b;
    margin-bottom: 1rem;
}

.advice-text {
    font-size: 1.3rem;
    font-weight: 600;
    color: #2d3748;
    line-height: 1.5;
}

.advice-section {
    transition: all 0.3s ease;
}

.hidden {
    display: none;
}

.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-dots:after {
    content: '';
    animation: dots 1.5s infinite;
}

@keyframes dots {
    0%, 20% { content: '.'; }
    40% { content: '..'; }
    60%, 100% { content: '...'; }
}

.input-group {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #00b09b;
    z-index: 10;
}

.form-control.with-icon {
    padding-left: 45px;
}

.pulse-animation {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
</style>

<div class="d-flex justify-content-center align-items-center py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <!-- Main Card -->
                <div class="glass-card position-relative" style="z-index: 10;">
                    
                    <!-- Header Compact -->
                    <div class="gradient-header text-white text-center py-3">
                        <div class="advice-icon">
                            <i class="bi bi-recycle"></i>
                        </div>
                        <h1 class="fw-bold h3 mb-1">AI Recycling Advisor ♻️</h1>
                        <p class="small mb-0 opacity-90">Get intelligent recycling recommendations powered by AI</p>
                    </div>

                    <!-- Body -->
                    <div class="card-body p-4">
                        <!-- Error Alert -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <strong>Please check the following:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Advice Form -->
                        <form id="recyclingForm" action="{{ route('ai.advice.recycling') }}" method="POST" class="mt-3">
                            @csrf

                            <div class="row g-3">
                                <!-- Waste Type -->
                                <div class="col-md-6">
                                    <label for="type" class="form-label fw-semibold">
                                        <i class="bi bi-tag me-2"></i>Waste Type
                                    </label>
                                    <div class="input-group">
                                        <i class="bi bi-tag input-icon"></i>
                                        <input type="text" name="type" id="type" 
                                               class="form-control with-icon" 
                                               placeholder="e.g., Plastic Bottles, Food Waste, Glass Jars"
                                               value="{{ old('type') }}"
                                               required>
                                    </div>
                                    <div class="form-text text-muted mt-1">
                                        Specify the type of waste material
                                    </div>
                                </div>

                                <!-- Category -->
                                <div class="col-md-6">
                                    <label for="category" class="form-label fw-semibold">
                                        <i class="bi bi-grid-3x3 me-2"></i>Category
                                    </label>
                                    <div class="input-group">
                                        <i class="bi bi-grid-3x3 input-icon"></i>
                                        <input type="text" name="category" id="category" 
                                               class="form-control with-icon" 
                                               placeholder="e.g., Recyclable, Organic, Glass"
                                               value="{{ old('category') }}"
                                               required>
                                    </div>
                                    <div class="form-text text-muted mt-1">
                                        Choose the waste category
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-ai pulse-animation">
                                    <i class="bi bi-robot me-2"></i>Get AI Recycling Advice
                                </button>
                            </div>
                        </form>

                        <!-- AI Advice Result Section -->
                        @if(isset($advice))
                        <div id="adviceSection" class="advice-section fade-in mt-4">
                            <div class="advice-card">
                                <div class="text-center">
                                    <div class="advice-icon">
                                        <i class="bi bi-lightbulb-fill"></i>
                                    </div>
                                    <div class="advice-title">
                                        AI Recycling Recommendation
                                    </div>
                                    <div class="advice-text">
                                        {{ $advice }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('recyclingForm');
    
    // Only add event listener if we don't already have advice (first load)
    @if(!isset($advice))
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const button = this.querySelector('button[type="submit"]');
        const originalText = button.innerHTML;
        
        // Show loading state for 3 seconds
        button.innerHTML = '<i class="bi bi-arrow-repeat spinner me-2"></i>Analyzing Waste<span class="loading-dots"></span>';
        button.disabled = true;

        // Create and show loading section
        const loadingSection = document.createElement('div');
        loadingSection.id = 'loadingSection';
        loadingSection.className = 'advice-section fade-in mt-4';
        loadingSection.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-success mb-3" style="width: 3rem; height: 3rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5 class="text-success">🤖 AI is analyzing your waste<span class="loading-dots"></span></h5>
                <p class="text-muted">Generating personalized recycling recommendations</p>
                <div class="progress mt-3" style="height: 6px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                </div>
            </div>
        `;
        
        // Remove existing advice section if present
        const existingAdvice = document.getElementById('adviceSection');
        if (existingAdvice) {
            existingAdvice.remove();
        }
        
        // Insert loading section after form
        form.parentNode.insertBefore(loadingSection, form.nextSibling);
        
        // Scroll to loading section
        loadingSection.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Animate progress bar for 3 seconds
        const progressBar = loadingSection.querySelector('.progress-bar');
        let width = 0;
        const interval = setInterval(() => {
            if (width >= 100) {
                clearInterval(interval);
            } else {
                width += 3.33;
                progressBar.style.width = width + '%';
            }
        }, 100);

        // Submit the form after 3 seconds
        setTimeout(() => {
            form.submit();
        }, 3000);
    });
    @endif

    // Interactive form inputs
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            if (this.value === '') {
                this.parentElement.classList.remove('focused');
            }
        });
    });
});
</script>
@endsection