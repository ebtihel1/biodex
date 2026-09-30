@extends('back.layout')

@section('content')
<div class="min-vh-100 d-flex justify-content-center align-items-center" style="background-color: #f8f9fa;">
    <div class="container py-5">
        <div class="card shadow-lg border-0 mx-auto" style="max-width: 1200px; width: 100%; border-radius: 1rem;">
            
            <!-- Header -->
            <div class="card-header bg-success text-white text-center py-4" style="border-radius: 1rem 1rem 0 0;">
                <h2 class="fw-bold mb-0">Edit Waste ♻️</h2>
            </div>

            <div class="card-body p-5">
                <!-- Error & Success Alerts -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Form -->
                <form action="{{ route('wastes.update', $waste->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <!-- Type -->
                        <div class="col-md-4">
                            <label for="type" class="form-label fw-semibold">Type</label>
                            <input type="text" name="type" id="type" value="{{ old('type', $waste->type) }}" class="form-control" required>
                        </div>

                        <!-- Weight -->
                        <div class="col-md-4">
                            <label for="weight" class="form-label fw-semibold">Weight (kg)</label>
                            <input type="number" step="0.01" name="weight" id="weight" value="{{ old('weight', $waste->weight) }}" class="form-control" required>
                        </div>

                        <!-- Status -->
                        <div class="col-md-4">
                            <label for="status" class="form-label fw-semibold">Status</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="recyclable" {{ old('status', $waste->status) == 'recyclable' ? 'selected' : '' }}>Recyclable</option>
                                <option value="reusable" {{ old('status', $waste->status) == 'reusable' ? 'selected' : '' }}>Reusable</option>
                            </select>
                        </div>

                        <!-- User -->
                        <div class="col-md-4">
                            <label for="user_id" class="form-label fw-semibold">User</label>
                            <select name="user_id" id="user_id" class="form-select" required>
                                @foreach(\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $waste->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Category -->
                        <div class="col-md-4">
                            <label for="waste_category_id" class="form-label fw-semibold">Waste Category</label>
                            <select name="waste_category_id" id="waste_category_id" class="form-select" required>
                                @foreach(\App\Models\WasteCategory::all() as $category)
                                    <option value="{{ $category->id }}" {{ old('waste_category_id', $waste->waste_category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Collection Point -->
                     
<div class="col-md-4">
    <label for="collection_point_id" class="form-label fw-semibold">Collection Point</label>
    <select name="collection_point_id" id="collection_point_id" class="form-select" required>
        <option value="" disabled {{ old('collection_point_id') ? '' : 'selected' }}>Select a Collection Point</option>
        @foreach($collectionPoints as $point)
            <option value="{{ $point->id }}" {{ old('collection_point_id') == $point->id ? 'selected' : '' }}>
                {{ $point->name }} - {{ $point->city }}
            </option>
        @endforeach
    </select>
</div>


                        <!-- Image -->
                        <div class="col-md-6">
                            <label for="image" class="form-label fw-semibold">Image</label>
                            <input type="file" name="image" id="image" class="form-control">
                            @if ($waste->image_path)
                                <div class="mt-2 small text-muted">
                                    Current image: 
                                    <a href="{{ asset('storage/' . $waste->image_path) }}" target="_blank">{{ $waste->image_path }}</a>
                                </div>
                            @endif
                        </div>

                        <!-- Description -->
                        <div class="col-md-6">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="description" rows="3" class="form-control" required>{{ old('description', $waste->description) }}</textarea>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="text-center mt-5">
                        <a href="{{ route('wastes.index') }}" class="btn btn-secondary px-4 me-2">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-save me-1"></i> Update Waste
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
