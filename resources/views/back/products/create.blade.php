@extends('back.layout')

@section('title', 'New Recycled Product')

@section('content')
<div class="container-fluid px-0">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left me-2"></i> Back
                </a>
                <div>
                    <h1 class="page-title text-success mb-1">
                        <i class="bi bi-plus-circle me-2"></i> New Recycled Product
                    </h1>
                    <p class="text-muted mb-0">Add a new product from recycling</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-plus me-2"></i> Product Information</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Nom du produit -->
                        <div class="mb-4">
                            <label for="name" class="form-label fw-medium">
                                <i class="bi bi-box-seam me-2 text-success"></i>Product Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" 
                                   placeholder="Ex: Recycled backpack"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-medium">
                                <i class="bi bi-text-paragraph me-2 text-success"></i>Description <span class="text-danger">*</span>
                            </label>
                            <textarea name="description" 
                                      id="description" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      rows="4" 
                                      placeholder="Describe the product in detail..."
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Prix -->
                            <div class="col-md-6 mb-4">
                                <label for="price" class="form-label fw-medium">
                                    <i class="bi bi-currency-dollar me-2 text-success"></i>Price (DT) <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       name="price" 
                                       id="price" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       value="{{ old('price') }}" 
                                       step="0.01" 
                                       min="0"
                                       placeholder="Ex: 29.99"
                                       required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Stock -->
                            <div class="col-md-6 mb-4">
                                <label for="stock_quantity" class="form-label fw-medium">
                                    <i class="bi bi-stack me-2 text-success"></i>Stock Quantity <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       name="stock_quantity" 
                                       id="stock_quantity" 
                                       class="form-control @error('stock_quantity') is-invalid @enderror" 
                                       value="{{ old('stock_quantity', 0) }}" 
                                       min="0"
                                       placeholder="Ex: 50"
                                       required>
                                @error('stock_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Catégorie -->
                            <div class="col-md-6 mb-4">
                                <label for="waste_category_id" class="form-label fw-medium">
                                    <i class="bi bi-tag me-2 text-success"></i>Category <span class="text-danger">*</span>
                                </label>
                                <select name="waste_category_id" 
                                        id="waste_category_id" 
                                        class="form-select @error('waste_category_id') is-invalid @enderror" 
                                        required>
                                    <option value="">-- Select a category --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('waste_category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('waste_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Processus de recyclage -->
                            <div class="col-md-6 mb-4">
                                <label for="recycling_process_id" class="form-label fw-medium">
                                    <i class="bi bi-arrow-repeat me-2 text-success"></i>Recycling Process
                                </label>
                                <select name="recycling_process_id" 
                                        id="recycling_process_id" 
                                        class="form-select @error('recycling_process_id') is-invalid @enderror">
                                    <option value="">-- None --</option>
                                    @foreach($recyclingProcesses as $process)
                                        <option value="{{ $process->id }}" {{ old('recycling_process_id') == $process->id ? 'selected' : '' }}>
                                            {{ $process->method }} - {{ $process->waste->type ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('recycling_process_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Only completed processes are available</small>
                            </div>
                        </div>

                        <!-- Image -->
                        <div class="mb-4">
                            <label for="image_path" class="form-label fw-medium">
                                <i class="bi bi-image me-2 text-success"></i>Product Image
                            </label>
                            <input type="file" 
                                   name="image_path" 
                                   id="image_path" 
                                   class="form-control @error('image_path') is-invalid @enderror" 
                                   accept="image/jpeg,image/png,image/jpg,image/gif">
                            @error('image_path')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Accepted formats: JPEG, PNG, JPG, GIF. Max size: 2 MB</small>
                            
                            <!-- Aperçu de l'image -->
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 300px;">
                            </div>
                        </div>

                        <!-- Spécifications -->
                        <div class="mb-4">
                            <label for="specifications" class="form-label fw-medium">
                                <i class="bi bi-list-check me-2 text-success"></i>Specifications
                            </label>
                            <textarea name="specifications" 
                                      id="specifications" 
                                      class="form-control @error('specifications') is-invalid @enderror" 
                                      rows="3" 
                                      placeholder='Ex: {"dimensions": "30x40cm", "weight": "500g", "material": "Recycled plastic"}'>{{ old('specifications') }}</textarea>
                            @error('specifications')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Enter specifications in text or JSON format</small>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-x-circle me-2"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-check-circle me-2"></i> Create Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.page-title { font-weight: 600; font-size: 1.75rem; }
.form-label { color: #495057; }
.form-control:focus, .form-select:focus { border-color: #198754; box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25); }
</style>

<script>
// Aperçu de l'image
document.getElementById('image_path').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endsection

