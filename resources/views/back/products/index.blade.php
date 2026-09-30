@extends('back.layout')

@section('title', 'Recycled Products')

@section('content')
<div class="container-fluid px-0">
    <!-- En-tête avec statistiques -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <h1 class="page-title text-success mb-2">
                        <i class="bi bi-box-seam me-2"></i> Recycled Products
                    </h1>
                    <p class="text-muted mb-0">Manage the catalog of products from recycling</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('products.create') }}" class="btn btn-success shadow-sm fw-medium px-4 py-2">
                        <i class="bi bi-plus-circle me-2"></i> New Product
                    </a>
                </div>
            </div>
        </div>

        <!-- Cartes de statistiques -->
        <div class="col-12">
            <div class="row g-3 mb-4">
                @php
                    $totalProducts = $products->count();
                    $available = $products->where('is_available', true)->count();
                    $unavailable = $products->where('is_available', false)->count();
                    $totalStock = $products->sum('stock_quantity');
                    $totalValue = $products->sum(function($p) { return $p->price * $p->stock_quantity; });
                @endphp

                <div class="col-md-3 col-sm-6">
                    <div class="card stat-card border-0 rounded-3 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Total Products</h6>
                                    <h3 class="text-success mb-0">{{ $totalProducts }}</h3>
                                </div>
                                <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                                    <i class="bi bi-box-seam text-success fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="card stat-card border-0 rounded-3 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Available</h6>
                                    <h3 class="text-primary mb-0">{{ $available }}</h3>
                                </div>
                                <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                                    <i class="bi bi-check-circle text-primary fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="card stat-card border-0 rounded-3 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Total Stock</h6>
                                    <h3 class="text-info mb-0">{{ $totalStock }}</h3>
                                </div>
                                <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                                    <i class="bi bi-stack text-info fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="card stat-card border-0 rounded-3 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Stock Value</h6>
                                    <h3 class="text-warning mb-0">{{ number_format($totalValue, 0) }} DT</h3>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                                    <i class="bi bi-currency-dollar text-warning fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Messages flash --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5 flex-shrink-0"></i>
            <div class="flex-grow-1">
                <span class="fw-medium">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close flex-shrink-0" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4 d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5 flex-shrink-0"></i>
            <div class="flex-grow-1">
                <span class="fw-medium">{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close flex-shrink-0" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($products->isEmpty())
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                <h4 class="text-muted mb-3">No products found</h4>
                <p class="text-muted mb-4">Start by adding your first recycled product</p>
                <a href="{{ route('products.create') }}" class="btn btn-success px-4">
                    <i class="bi bi-plus-circle me-2"></i> Create Product
                </a>
            </div>
        </div>
    @else
        <!-- Grille de produits -->
        <div class="row g-3">
            @foreach ($products as $product)
                <div class="col-lg-4 col-md-6">
                    <div class="card product-card shadow-sm border-0 rounded-3 h-100">
                        <!-- Image -->
                        <div class="product-image-container position-relative">
                            @if($product->image_path)
                                <img src="{{ asset('storage/' . $product->image_path) }}" 
                                     class="card-img-top product-image" 
                                     alt="{{ $product->name }}">
                            @else
                                <div class="product-image-placeholder bg-light d-flex align-items-center justify-content-center">
                                    <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
                                </div>
                            @endif
                            
                            <!-- Badge disponibilité -->
                            <div class="position-absolute top-0 end-0 m-3">
                                @if($product->is_available)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i> Available
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i> Unavailable
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Badge stock -->
                            <div class="position-absolute bottom-0 start-0 m-3">
                                <span class="badge bg-dark bg-opacity-75">
                                    <i class="bi bi-stack me-1"></i> Stock: {{ $product->stock_quantity }}
                                </span>
                            </div>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <!-- Nom et catégorie -->
                            <h5 class="card-title fw-bold text-success mb-2">{{ $product->name }}</h5>
                            <p class="text-muted small mb-2">
                                <i class="bi bi-tag me-1"></i>
                                {{ $product->category->name ?? 'Uncategorized' }}
                            </p>
                            
                            <!-- Description -->
                            <p class="card-text text-muted small mb-3 flex-grow-1">
                                {{ Str::limit($product->description, 80) }}
                            </p>
                            
                            <!-- Prix -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h4 class="text-success mb-0">{{ number_format($product->price, 2) }} DT</h4>
                                </div>
                                @if($product->recyclingProcess)
                                    <small class="text-muted">
                                        <i class="bi bi-arrow-repeat me-1"></i>
                                        {{ $product->recyclingProcess->method }}
                                    </small>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="d-flex gap-2">
                                <a href="{{ route('products.edit', $product->id) }}" 
                                   class="btn btn-warning btn-sm flex-fill">
                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                </a>
                                <form action="{{ route('products.destroy', $product->id) }}" 
                                      method="POST" 
                                      class="flex-fill"
                                      onsubmit="return confirm('Are you sure you want to delete this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm w-100">
                                        <i class="bi bi-trash3 me-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
.page-title { font-weight: 600; font-size: 1.75rem; }
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; transition: all 0.3s ease; }

.product-card {
    transition: all 0.3s ease;
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15) !important;
}

.product-image-container {
    height: 200px;
    overflow: hidden;
    background: #f8f9fa;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-image-placeholder {
    width: 100%;
    height: 200px;
}

.card-title {
    font-size: 1.1rem;
}
</style>

<script>
// Initialiser les tooltips Bootstrap si nécessaire
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
@endsection

