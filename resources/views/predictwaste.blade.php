@extends('back.layout')

@section('content')
<style>
.glass-card {
    background: white;
    border-radius: 20px;
    border: 1px solid rgba(0, 0, 0, 0.1);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.gradient-header {
    background: linear-gradient(135deg, #00b09b, #96c93d);
    position: relative;
    overflow: hidden;
    padding: 1.5rem 1rem !important;
}

.gradient-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
}

.gradient-header .ai-icon {
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
    background: white;
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
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-ai {
    background: linear-gradient(135deg, #00b09b, #96c93d);
    border: none;
    border-radius: 50px;
    padding: 14px 40px;
    font-weight: 600;
    font-size: 16px;
    color: white;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(0, 176, 155, 0.3);
    position: relative;
    overflow: hidden;
}

.btn-ai:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 35px rgba(0, 176, 155, 0.4);
    background: linear-gradient(135deg, #00a18b, #8bc34a);
}

.prediction-card {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border: 2px solid #00b09b;
    border-radius: 16px;
    color: #2d3748;
    box-shadow: 0 10px 30px rgba(0, 176, 155, 0.15);
    padding: 2rem;
    position: relative;
    overflow: hidden;
}

.prediction-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, #00b09b, #96c93d);
}

.ai-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, #00b09b, #96c93d);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
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

.fade-in {
    animation: fadeIn 0.6s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<div class="min-vh-100 d-flex justify-content-center align-items-center bg-white">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10 col-md-12">
                <!-- Main Card -->
                <div class="glass-card">
                    <!-- Header -->
                    <div class="gradient-header text-white text-center py-3 position-relative">
                        <div class="ai-icon">
                            <i class="bi bi-cpu-fill"></i>
                        </div>
                        <h1 class="fw-bold h3 mb-1">AI Waste Prediction ♻️</h1>
                        <p class="small mb-0 opacity-90">Intelligent recycling recommendations powered by AI</p>
                    </div>

                    <!-- Body -->
                    <div class="card-body p-4 p-md-5">
                        <!-- Error Alert -->
                        <div id="errorDiv" class="alert alert-danger alert-dismissible fade show d-none" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <span id="errorText"></span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>

                        <!-- Prediction Form -->
                        <form id="predictForm" class="mt-4">
                            @csrf

                            <div class="row g-4">
                                <!-- Waste Type -->
                                <div class="col-md-6">
                                    <label for="type" class="form-label">
                                        <i class="bi bi-tag me-2"></i>Waste Type
                                    </label>
                                    <div class="input-group">
                                        <i class="bi bi-tag input-icon"></i>
                                        <input type="text" name="type" id="type" 
                                               class="form-control with-icon" 
                                               placeholder="e.g., Plastic Bottles, Glass Jars, Food Waste"
                                               required>
                                    </div>
                                </div>

                                <!-- Weight -->
                                <div class="col-md-6">
                                    <label for="weight" class="form-label">
                                        <i class="bi bi-speedometer2 me-2"></i>Weight (kg)
                                    </label>
                                    <div class="input-group">
                                        <i class="bi bi-speedometer2 input-icon"></i>
                                        <input type="number" step="0.01" name="weight" id="weight" 
                                               class="form-control with-icon" 
                                               placeholder="0.00"
                                               required>
                                    </div>
                                </div>

                                <!-- Category -->
                                <div class="col-md-6">
                                    <label for="category" class="form-label">
                                        <i class="bi bi-grid-3x3 me-2"></i>Category
                                    </label>
                                    <div class="input-group">
                                        <i class="bi bi-grid-3x3 input-icon"></i>
                                        <input type="text" name="category" id="category" 
                                               class="form-control with-icon" 
                                               placeholder="e.g., Recyclable, Organic, Glass"
                                               required>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="col-md-6">
                                    <label for="description" class="form-label">
                                        <i class="bi bi-card-text me-2"></i>Description
                                    </label>
                                    <div class="input-group">
                                        <i class="bi bi-card-text input-icon"></i>
                                        <input type="text" name="description" id="description" 
                                               class="form-control with-icon" 
                                               placeholder="Additional details about the waste">
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center mt-5">
                                <button type="submit" class="btn btn-ai pulse-animation">
                                    <i class="bi bi-robot me-2"></i>Analyze with AI
                                </button>
                            </div>
                        </form>

                        <!-- Loading Indicator -->
                        <div id="loadingDiv" class="text-center mt-4 d-none">
                            <div class="spinner-border text-success" style="width: 3rem; height: 3rem;" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-3">AI is analyzing your waste data...</p>
                        </div>

                        <!-- Prediction Result -->
                        <div id="predictionDiv" class="prediction-card text-center mt-4 d-none fade-in">
                            <div class="ai-icon">
                                <i class="bi bi-lightbulb-fill"></i>
                            </div>
                            <h4 class="fw-bold text-success mb-3">AI Prediction Result</h4>
                            <p class="fs-5 fw-semibold mb-0" id="predictionText"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
document.getElementById('predictForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const errorDiv = document.getElementById('errorDiv');
    const errorText = document.getElementById('errorText');
    const predictionDiv = document.getElementById('predictionDiv');
    const predictionText = document.getElementById('predictionText');
    const loadingDiv = document.getElementById('loadingDiv');

    // Show loading, hide other elements
    loadingDiv.classList.remove('d-none');
    predictionDiv.classList.add('d-none');
    errorDiv.classList.add('d-none');

    fetch("{{ route('ai.predict') }}", {
        method: "POST",
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        loadingDiv.classList.add('d-none');
        
        if (data.prediction) {
            predictionText.innerText = data.prediction;
            predictionDiv.classList.remove('d-none');
            errorDiv.classList.add('d-none');
            
            // Smooth scroll to result
            predictionDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            throw new Error('No prediction received from the AI');
        }
    })
    .catch(error => {
        loadingDiv.classList.add('d-none');
        errorText.innerText = error.message || "An error occurred while communicating with the AI.";
        errorDiv.classList.remove('d-none');
        predictionDiv.classList.add('d-none');
    });
});

// Add focus effects to form inputs
document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
    });
    
    input.addEventListener('blur', function() {
        this.parentElement.classList.remove('focused');
    });
});
</script>
@endsection