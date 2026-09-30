@extends('front.layout')

@section('content')
<div class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-success">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('front.products.index') }}" class="text-success">Products</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Image et galerie -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                @if($product->image_path)
                    <img src="{{ asset('storage/' . $product->image_path) }}" 
                         class="img-fluid product-main-image" 
                         alt="{{ $product->name }}">
                @else
                    <div class="product-main-placeholder bg-light d-flex align-items-center justify-content-center">
                        <i class="bi bi-image text-muted" style="font-size: 5rem;"></i>
                    </div>
                @endif
            </div>
        </div>

        <!-- Informations produit -->
        <div class="col-lg-6">
            <div class="product-details">
                <!-- Catégorie -->
                <div class="mb-3">
                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 fs-6">
                        <i class="bi bi-tag me-1"></i>
                        {{ $product->category->name ?? 'Unclassified' }}
                    </span>
                </div>

                <!-- Nom -->
                <h1 class="display-5 fw-bold text-dark mb-3">{{ $product->name }}</h1>

                <!-- Prix et stock -->
                <div class="d-flex align-items-center gap-4 mb-4">
                    <h2 class="text-success mb-0 fw-bold">{{ number_format($product->price, 2) }} DT</h2>
                    <div>
                        @if($product->stock_quantity > 10)
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i> In Stock ({{ $product->stock_quantity }})
                            </span>
                        @elseif($product->stock_quantity > 0)
                            <span class="badge bg-warning">
                                <i class="bi bi-exclamation-triangle me-1"></i> Limited Stock ({{ $product->stock_quantity }})
                            </span>
                        @else
                            <span class="badge bg-danger">
                                <i class="bi bi-x-circle me-1"></i> Out of Stock
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-info-circle me-2 text-success"></i> Description
                    </h5>
                    <p class="text-muted lh-lg">{{ $product->description }}</p>
                </div>

                <!-- Processus de recyclage -->
                @if($product->recyclingProcess)
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-arrow-repeat me-2 text-success"></i> Recycling Process
                        </h5>
                        <div class="card bg-light border-0 p-3">
                            <div class="row g-3">
                                <div class="col-6">
                                    <small class="text-muted d-block">Method</small>
                                    <strong>{{ $product->recyclingProcess->method }}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Source Waste</small>
                                    <strong>{{ $product->recyclingProcess->waste->type ?? 'N/A' }}</strong>
                                </div>
                                @if($product->recyclingProcess->output_quality)
                                    <div class="col-6">
                                        <small class="text-muted d-block">Quality</small>
                                        <strong>{{ $product->recyclingProcess->output_quality }}</strong>
                                    </div>
                                @endif
                                @if($product->recyclingProcess->output_quantity)
                                    <div class="col-6">
                                        <small class="text-muted d-block">Quantity Produced</small>
                                        <strong>{{ $product->recyclingProcess->output_quantity }} kg</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Spécifications -->
                @if($product->specifications && is_array($product->specifications))
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-list-check me-2 text-success"></i> Specifications
                        </h5>
                        <ul class="list-unstyled">
                            @foreach($product->specifications as $key => $value)
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    <strong>{{ ucfirst($key) }}:</strong> {{ $value }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Bouton d'action -->
                <div class="d-grid gap-2 mb-4">
                    @if($product->stock_quantity > 0)
                        <button class="btn btn-success btn-lg" onclick="alert('Fonctionnalité de commande à venir !')">
                            <i class="bi bi-cart-plus me-2"></i> Add to Cart
                        </button>
                    @else
                        <button class="btn btn-secondary btn-lg" disabled>
                            <i class="bi bi-x-circle me-2"></i> Product Unavailable
                        </button>
                    @endif
                </div>

                <!-- Impact écologique -->
                <div class="alert alert-success border-0 shadow-sm">
                    <h6 class="alert-heading fw-bold">
                        <i class="bi bi-leaf me-2"></i> Environmental Impact
                    </h6>
                    <p class="mb-0 small">
                        By purchasing this recycled product, you participate in the circular economy and contribute to waste reduction. 
                        Thank you for your commitment to the environment! 🌍
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Produits similaires -->
    @if($similarProducts->isNotEmpty())
        <div class="mt-5">
            <h3 class="fw-bold mb-4">
                <i class="bi bi-box-seam me-2 text-success"></i> Similar Products
            </h3>
            <div class="row g-4">
                @foreach($similarProducts as $similar)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card product-card h-100 shadow-sm border-0 rounded-3">
                            <!-- Image -->
                            <div class="product-image-wrapper position-relative overflow-hidden">
                                @if($similar->image_path)
                                    <img src="{{ asset('storage/' . $similar->image_path) }}" 
                                         class="card-img-top product-image" 
                                         alt="{{ $similar->name }}">
                                @else
                                    <div class="product-placeholder bg-light d-flex align-items-center justify-content-center">
                                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold text-dark mb-2">{{ $similar->name }}</h5>
                                <p class="card-text text-muted small flex-grow-1 mb-3">
                                    {{ Str::limit($similar->description, 60) }}
                                </p>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="text-success mb-0">{{ number_format($similar->price, 2) }} DT</h5>
                                </div>
                                <a href="{{ route('front.products.show', $similar->id) }}" 
                                   class="btn btn-outline-success w-100">
                                    <i class="bi bi-eye me-1"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<style>
.product-main-image {
    width: 100%;
    max-height: 500px;
    object-fit: cover;
}

.product-main-placeholder {
    width: 100%;
    height: 500px;
}

.product-card {
    transition: all 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15) !important;
}

.product-image-wrapper {
    height: 200px;
    background: #f8f9fa;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.1);
}

.product-placeholder {
    width: 100%;
    height: 200px;
}

.breadcrumb {
    background: transparent;
    padding: 0;
}

.breadcrumb-item + .breadcrumb-item::before {
    color: #6c757d;
}
</style>
@endsection

