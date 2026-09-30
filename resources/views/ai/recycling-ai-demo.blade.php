@extends('back.layout')

@section('title', 'AI Demo - Recycling Module')

@section('content')
<!-- Token CSRF pour les requêtes AJAX -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid px-0">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center mb-4">
                <div>
                    <h1 class="page-title text-success mb-1">
                        <i class="bi bi-robot me-2"></i> AI Demo - Recycling Module
                    </h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Statut du service IA -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-1">
                                <i class="bi bi-server me-2"></i> AI Service
                            </h5>
                            <p class="text-muted mb-0">Artificial intelligence service status</p>
                        </div>
                        <div>
                            <span id="ai-status" class="badge bg-secondary">Checking...</span>
                            <button class="btn btn-outline-primary btn-sm ms-2" onclick="checkAIStatus()">
                                <i class="bi bi-arrow-clockwise"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- 1. Classification de Déchets -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-search me-2"></i> Waste Classification
                    </h5>
                </div>
                <div class="card-body">
                    <form id="classify-form">
                        <div class="mb-3">
                            <label class="form-label">Waste Type</label>
                            <input type="text" class="form-control" name="type" placeholder="Ex: plastic bottle" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Weight (kg)</label>
                            <input type="number" class="form-control" name="weight" step="0.1" placeholder="Ex: 0.5" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Detailed waste description..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-robot me-2"></i> Classify with AI
                        </button>
                    </form>
                    
                    <div id="classify-result" class="mt-3" style="display: none;">
                        <div class="alert alert-info">
                            <h6>Classification result:</h6>
                            <div id="classify-content"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Prédiction de Qualité -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-star me-2"></i> Quality Prediction
                    </h5>
                </div>
                <div class="card-body">
                    <form id="quality-form">
                        <div class="mb-3">
                            <label class="form-label">Waste Type</label>
                            <select class="form-select" name="waste_type" required>
                                <option value="">Select...</option>
                                <option value="Plastic">Plastic</option>
                                <option value="Glass">Glass</option>
                                <option value="Paper">Paper</option>
                                <option value="Metal">Metal</option>
                                <option value="Organic">Organic</option>
                                <option value="Electronic">Electronic</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Recycling Method</label>
                            <select class="form-select" name="recycling_method" required>
                                <option value="">Select...</option>
                                <option value="Mechanical recycling">Mechanical recycling</option>
                                <option value="Melting and molding">Melting and molding</option>
                                <option value="Pulping and reforming">Pulping and reforming</option>
                                <option value="Composting">Composting</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Waste Condition</label>
                            <select class="form-select" name="waste_condition" required>
                                <option value="excellent">Excellent</option>
                                <option value="good" selected>Good</option>
                                <option value="fair">Average</option>
                                <option value="poor">Poor</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Storage Days</label>
                            <input type="number" class="form-control" name="storage_days" value="0" min="0">
                        </div>
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="bi bi-graph-up me-2"></i> Predict Quality
                        </button>
                    </form>
                    
                    <div id="quality-result" class="mt-3" style="display: none;">
                        <div class="alert alert-warning">
                            <h6>Quality prediction:</h6>
                            <div id="quality-content"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Estimation de Prix -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-currency-dollar me-2"></i> Price Estimation
                    </h5>
                </div>
                <div class="card-body">
                    <form id="price-form">
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" class="form-control" name="product_name" placeholder="Ex: Recycled backpack" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Waste Category</label>
                            <select class="form-select" name="waste_category" required>
                                <option value="">Select...</option>
                                <option value="Plastic">Plastic</option>
                                <option value="Glass">Glass</option>
                                <option value="Paper">Paper</option>
                                <option value="Metal">Metal</option>
                                <option value="Organic">Organic</option>
                                <option value="Electronic">Electronic</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quality Score (0-100)</label>
                            <input type="number" class="form-control" name="quality_score" value="70" min="0" max="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Recycling Cost (DT)</label>
                            <input type="number" class="form-control" name="recycling_cost" value="10" step="0.1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Market Demand (0-100)</label>
                            <input type="number" class="form-control" name="market_demand" value="50" min="0" max="100">
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-calculator me-2"></i> Estimate Price
                        </button>
                    </form>
                    
                    <div id="price-result" class="mt-3" style="display: none;">
                        <div class="alert alert-success">
                            <h6>Price estimation:</h6>
                            <div id="price-content"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Génération de Description -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i> Description Generation
                    </h5>
                </div>
                <div class="card-body">
                    <form id="description-form">
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" class="form-control" name="product_name" placeholder="Ex: Eco-friendly backpack" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Source Material</label>
                            <input type="text" class="form-control" name="source_material" placeholder="Ex: Plastic bottles" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Recycling Method</label>
                            <input type="text" class="form-control" name="recycling_method" placeholder="Ex: Mechanical recycling" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Specifications (JSON)</label>
                            <textarea class="form-control" name="specifications" rows="3" placeholder='{"dimensions": "30x40cm", "weight": "500g"}'></textarea>
                        </div>
                        <button type="submit" class="btn btn-info w-100">
                            <i class="bi bi-magic me-2"></i> Generate Description
                        </button>
                    </form>
                    
                    <div id="description-result" class="mt-3" style="display: none;">
                        <div class="alert alert-info">
                            <h6>Generated description:</h6>
                            <div id="description-content"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Vérifier l'état du service IA au chargement
document.addEventListener('DOMContentLoaded', function() {
    checkAIStatus();
});

// Vérifier l'état du service IA
function checkAIStatus() {
    fetch('/ai/recycling/health')
        .then(response => response.json())
        .then(data => {
            const statusElement = document.getElementById('ai-status');
            if (data.status === 'healthy') {
                statusElement.className = 'badge bg-success';
                statusElement.textContent = 'AI Service Active';
            } else {
                statusElement.className = 'badge bg-danger';
                statusElement.textContent = 'AI Service Unavailable';
            }
        })
        .catch(error => {
            const statusElement = document.getElementById('ai-status');
            statusElement.className = 'badge bg-danger';
            statusElement.textContent = 'AI Service Unavailable';
        });
}

// Classification de déchets
document.getElementById('classify-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    console.log('Form submitted!'); // Debug
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    console.log('Data to send:', data); // Debug
    
    fetch('/ai/recycling/classify-waste', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('Response status:', response.status); // Debug
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data); // Debug
        if (data.success) {
            const result = data.classification;
            document.getElementById('classify-content').innerHTML = `
                <strong>Category:</strong> ${result.category}<br>
                <strong>Confidence:</strong> ${(result.confidence * 100).toFixed(1)}%<br>
                <strong>Recommended method:</strong> ${result.recommended_method}<br>
                <strong>Recyclability score:</strong> ${result.recyclability_score}%
            `;
            document.getElementById('classify-result').style.display = 'block';
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Fetch error:', error); // Debug
        alert('AI service connection error: ' + error.message);
    });
});

// Prédiction de qualité
document.getElementById('quality-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    fetch('/ai/recycling/predict-quality', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const result = data.quality_prediction;
            document.getElementById('quality-content').innerHTML = `
                <strong>Score:</strong> ${result.score}%<br>
                <strong>Level:</strong> <span class="badge bg-${result.color}">${result.level}</span><br>
                <strong>Recommendations:</strong><br>
                <ul>${result.recommendations.map(rec => `<li>${rec}</li>`).join('')}</ul>
            `;
            document.getElementById('quality-result').style.display = 'block';
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        alert('AI service connection error');
    });
});

// Estimation de prix
document.getElementById('price-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    fetch('/ai/recycling/estimate-price', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const result = data.price_estimation;
            document.getElementById('price-content').innerHTML = `
                <strong>Base price:</strong> ${result.base_price} DT<br>
                <strong>Estimated price:</strong> ${result.estimated_price} DT<br>
                <strong>Final price (with margin):</strong> <span class="fw-bold text-success">${result.final_price} DT</span><br>
                <strong>Profit margin:</strong> ${result.profit_margin} DT<br>
                <strong>Confidence:</strong> ${(result.confidence * 100).toFixed(1)}%
            `;
            document.getElementById('price-result').style.display = 'block';
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        alert('AI service connection error');
    });
});

// Génération de description
document.getElementById('description-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // Parser les spécifications JSON
    if (data.specifications) {
        try {
            data.specifications = JSON.parse(data.specifications);
        } catch (e) {
            data.specifications = {};
        }
    }
    
    fetch('/ai/recycling/generate-description', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('description-content').innerHTML = `
                <div class="p-3 bg-light rounded">
                    ${data.description}
                </div>
            `;
            document.getElementById('description-result').style.display = 'block';
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        alert('AI service connection error');
    });
});
</script>
@endsection
