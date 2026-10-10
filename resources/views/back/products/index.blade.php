@extends('back.layout')

@section('title', 'Recycled Products')

@section('content')
<style>
/* ===== Statistics (uniformisé) ===== */
.product-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stats-wrapper {
    background: linear-gradient(135deg, #f2dd94, #e8c471);
    border-radius: 25px;
    padding: 2rem;
    color: #1b4332;
    margin-bottom: 2.5rem;
}

.stats-wrapper .stat-card {
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    padding: 1.8rem;
    backdrop-filter: blur(12px);
    box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
    color: #fff;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}
.stats-wrapper .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}
.stats-wrapper .stat-card::before {
    content: "";
    position: absolute;
    top: -40%; right: -40%;
    width: 200%; height: 200%;
    background: radial-gradient(circle at top right, rgba(255,255,255,0.2), transparent 70%);
    transform: rotate(25deg);
}
.stats-wrapper .stat-icon { font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.9; color: #1b4332; }
.stats-wrapper .stat-number { font-size: 2rem; font-weight: 700; color: #1b4332; }
.stats-wrapper .stat-label { opacity: 0.9; font-size: 0.95rem; letter-spacing: 0.5px; color: #1b4332; }

/* ===== Product Card ===== */
.product-card {
    transition: all 0.3s ease;
    overflow: hidden;
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15) !important;
}

.product-image-container {
    height: 200px;
    overflow: hidden;
    background: #f8f9fa;
    position: relative;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}
.product-card:hover .product-image { transform: scale(1.05); }

.product-image-placeholder {
    width: 100%;
    height: 200px;
}

.card-title { font-size: 1.1rem; }

/* ===== Filter Bar ===== */
.filter-bar {
    background: #ffffff;
    border-radius: 15px;
    box-shadow: 0 6px 30px rgba(0,0,0,0.06);
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
}
.filter-bar .form-control,
.filter-bar .form-select { border-radius: 10px; }

/* ===== Page Header ===== */
.page-header {
    display: flex; flex-wrap: wrap; justify-content: space-between;
    align-items: center; margin-bottom: 1.5rem;
}
.btn-add {
    background: linear-gradient(135deg, #00c9a7, #007bff);
    border: none; color: white; border-radius: 30px;
    padding: 0.7rem 1.8rem; font-weight: 600;
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
}
.btn-add:hover {
    background: linear-gradient(135deg, #007bff, #00c9a7);
    transform: translateY(-2px); color: #fff;
}

/* ===== Sort selector (because products use grid, no sortable headers) ===== */
.sort-selector {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

/* ===== Pagination ===== */
.pagination { margin-bottom: 0; }
.pagination .page-link { color: #198754; }
.pagination .page-item.active .page-link {
    background-color: #198754; border-color: #198754; color: #fff;
}

/* ===== Fade in ===== */
.fade-in-up { animation: fadeInUp 0.6s ease-out; }
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(25px); }
    to   { opacity: 1; transform: translateY(0); }
}

@media (max-width: 768px) {
    .stats-wrapper { padding: 1rem; }
    .btn-add { width: 100%; margin-top: 1rem; }
}
</style>

@php
    $exportParams = array_filter(
        request()->only('search', 'available', 'category', 'recycling_process', 'sort', 'direction'),
        fn ($value) => $value !== null && $value !== ''
    );
@endphp

<div class="container-fluid py-4 fade-in-up">

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('import_errors') && count(session('import_errors')))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Lignes ignorées :</strong>
            <ul class="mb-0 mt-2">
                @foreach (session('import_errors') as $importError)
                    <li>{{ $importError }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Statistics --}}
    <div class="stats-wrapper fade-in-up">
        <div class="product-stats">
            <div class="stat-card">
                <i class="bi bi-box-seam stat-icon"></i>
                <div class="stat-number">{{ number_format((int) $summary->total_count) }}</div>
                <div class="stat-label">Total Products</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-check-circle stat-icon"></i>
                <div class="stat-number">{{ number_format((int) $summary->available_count) }}</div>
                <div class="stat-label">Available</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-stack stat-icon"></i>
                <div class="stat-number">{{ number_format((int) $summary->total_stock) }}</div>
                <div class="stat-label">Total Stock</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-currency-dollar stat-icon"></i>
                <div class="stat-number">{{ number_format((float) $summary->total_value, 0) }} DT</div>
                <div class="stat-label">Stock Value</div>
            </div>
        </div>
    </div>
    {{-- Header --}}
    <div class="page-header">
        <div>
            <h2 class="fw-bold text-success mb-1">
                <i class="bi bi-box-seam me-2"></i>Recycled Products
            </h2>
            <p class="text-muted mb-0">Manage the catalog of products from recycling</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('products.export.csv', $exportParams) }}" class="btn btn-outline-success">
                <i class="bi bi-filetype-csv me-1"></i>Export CSV
            </a>
            <a href="{{ route('products.export.pdf', $exportParams) }}" class="btn btn-outline-danger">
                <i class="bi bi-filetype-pdf me-1"></i>Export PDF
            </a>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-upload me-1"></i>Import CSV
            </button>
            <a href="{{ route('products.create') }}" class="btn btn-add">
                <i class="bi bi-plus-circle-fill me-2"></i>New Product
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('products.index') }}" class="filter-bar">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label class="form-label mb-1 small text-muted">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control" placeholder="Name, description, category...">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1 small text-muted">Availability</label>
                <select name="available" class="form-select">
                    <option value="">All</option>
                    <option value="1" @selected(request('available') === '1')>Available</option>
                    <option value="0" @selected(request('available') === '0')>Unavailable</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1 small text-muted">Category</label>
                <select name="category" class="form-select">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1 small text-muted">Sort by</label>
                <select name="sort" class="form-select">
                    <option value="id"              @selected(request('sort') === 'id')>ID</option>
                    <option value="name"            @selected(request('sort') === 'name')>Name</option>
                    <option value="price"           @selected(request('sort') === 'price')>Price</option>
                    <option value="stock_quantity"  @selected(request('sort') === 'stock_quantity')>Stock</option>
                    <option value="is_available"    @selected(request('sort') === 'is_available')>Availability</option>
                    <option value="created_at"      @selected(request('sort') === 'created_at')>Created</option>
                </select>
            </div>
            <div class="col-6 col-md-1">
                <label class="form-label mb-1 small text-muted">Dir.</label>
                <select name="direction" class="form-select">
                    <option value="asc"  @selected(request('direction') !== 'desc')>↑</option>
                    <option value="desc" @selected(request('direction') === 'desc')>↓</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-success flex-fill">
                    <i class="bi bi-funnel-fill me-1"></i>Filter
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary" title="Reset filters">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </div>
    </form>

    @if ($products->isEmpty())
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                <h4 class="text-muted mb-3">No products found</h4>
                <p class="text-muted mb-4">
                    {{ request('search') || request('available') || request('category')
                        ? 'Try changing or clearing your filters.'
                        : 'Start by adding your first recycled product.' }}
                </p>
                @if(!request('search') && !request('available') && !request('category'))
                    <a href="{{ route('products.create') }}" class="btn btn-success px-4">
                        <i class="bi bi-plus-circle me-2"></i> Create Product
                    </a>
                @else
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-x-circle me-2"></i> Clear Filters
                    </a>
                @endif
            </div>
        </div>
    @else
        {{-- Products Grid --}}
        <div class="row g-3">
            @foreach ($products as $product)
                <div class="col-lg-4 col-md-6">
                    <div class="card product-card shadow-sm border-0 rounded-3 h-100">
                        <div class="product-image-container">
                            @if($product->image_path)
                                <img src="{{ asset('storage/' . $product->image_path) }}"
                                     class="card-img-top product-image"
                                     alt="{{ $product->name }}">
                            @else
                                <div class="product-image-placeholder bg-light d-flex align-items-center justify-content-center">
                                    <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
                                </div>
                            @endif

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

                            <div class="position-absolute bottom-0 start-0 m-3">
                                <span class="badge bg-dark bg-opacity-75">
                                    <i class="bi bi-stack me-1"></i> Stock: {{ $product->stock_quantity }}
                                </span>
                            </div>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold text-success mb-2">{{ $product->name }}</h5>
                            <p class="text-muted small mb-2">
                                <i class="bi bi-tag me-1"></i>
                                {{ $product->category->name ?? 'Uncategorized' }}
                            </p>

                            <p class="card-text text-muted small mb-3 flex-grow-1">
                                {{ \Illuminate\Support\Str::limit($product->description, 80) }}
                            </p>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="text-success mb-0">{{ number_format($product->price, 2) }} DT</h4>
                                @if($product->recyclingProcess)
                                    <small class="text-muted">
                                        <i class="bi bi-arrow-repeat me-1"></i>
                                        {{ $product->recyclingProcess->method }}
                                    </small>
                                @endif
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('products.show', $product->id) }}"
                                   class="btn btn-info btn-sm flex-fill text-white">
                                    <i class="bi bi-eye me-1"></i> View
                                </a>
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
                                        <i class="bi bi-trash3 me-1"></i> Del
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mt-4 gap-2">
            <div class="text-muted small">
                Showing {{ $products->firstItem() }}–{{ $products->lastItem() }}
                of {{ $products->total() }} entries
            </div>
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

{{-- Import Modal --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="bi bi-upload me-2"></i>Import Products
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">CSV file</label>
                        <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv" required>
                        @error('csv_file')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <p class="text-muted small mb-0">
                        The first line may contain headers (name, description, price, stock_quantity,
                        waste_category_id, recycling_process_id, specifications, is_available).
                        Use the CSV export as a template. Separator <code>;</code> or <code>,</code> is detected automatically.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-upload me-1"></i>Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

        @if ($errors->has('csv_file'))
            new bootstrap.Modal(document.getElementById('importModal')).show();
        @endif
    });
</script>
@endsection