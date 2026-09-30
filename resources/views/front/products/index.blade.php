@extends('front.layout')

@section('content')
<div class="container my-5">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12 text-center mb-4">
            <h1 class="display-4 fw-bold text-success">
                <i class="bi bi-shop me-2"></i> Recycled Products
            </h1>
            <p class="lead text-muted">Discover our catalog of products from the circular economy</p>
        </div>
    </div>

    <!-- Filtres et Recherche -->
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('front.products.index') }}" class="row g-3">
                <!-- Recherche -->
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-success text-white border-success">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Search for a product..." 
                               value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Catégorie -->
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">All categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tri -->
                <div class="col-md-2">
                    <select name="sort" class="form-select">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name (A-Z)</option>
                    </select>
                </div>

                <!-- Bouton -->
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Résultats -->
    <div class="row mb-3">
        <div class="col-12">
            <p class="text-muted">
                <i class="bi bi-grid-3x3-gap me-2"></i>
                {{ $products->total() }} product(s) found
            </p>
        </div>
    </div>

    @if($products->isEmpty())
        <!-- Aucun produit -->
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                <h4 class="text-muted mb-3">No products available</h4>
                <p class="text-muted mb-4">Come back later to discover our new recycled products</p>
            </div>
        </div>
    @else
        <!-- Grille de produits -->
        <div class="row g-4 mb-4">
            @foreach($products as $product)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card product-card h-100 shadow-sm border-0 rounded-3">
                        <!-- Image -->
                        <div class="product-image-wrapper position-relative overflow-hidden">
                            @if($product->image_path)
                                <img src="{{ asset('storage/' . $product->image_path) }}" 
                                     class="card-img-top product-image" 
                                     alt="{{ $product->name }}">
                            @else
                                <div class="product-placeholder bg-light d-flex align-items-center justify-content-center">
                                    <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                            
                            <!-- Badge catégorie -->
                            <div class="position-absolute top-0 start-0 m-2">
                                <span class="badge bg-success">
                                    {{ $product->category->name ?? 'Unclassified' }}
                                </span>
                            </div>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <!-- Nom -->
                            <h5 class="card-title fw-bold text-dark mb-2">
                                {{ $product->name }}
                            </h5>
                            
                            <!-- Description courte -->
                            <p class="card-text text-muted small flex-grow-1 mb-3">
                                {{ Str::limit($product->description, 80) }}
                            </p>
                            
                            <!-- Prix et stock -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h4 class="text-success mb-0 fw-bold">
                                        {{ number_format($product->price, 2) }} DT
                                    </h4>
                                </div>
                                <div>
                                    <small class="text-muted">
                                        <i class="bi bi-box-seam me-1"></i>
                                        Stock: {{ $product->stock_quantity }}
                                    </small>
                                </div>
                            </div>

                            <!-- Bouton -->
                            <a href="{{ route('front.products.show', $product->id) }}" 
                               class="btn btn-outline-success w-100">
                                <i class="bi bi-eye me-1"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<style>
.product-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
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

.card-title {
    font-size: 1rem;
    min-height: 2.5rem;
}

.btn-outline-success:hover {
    color: white;
    background-color: #198754;
}
</style>
@endsection

