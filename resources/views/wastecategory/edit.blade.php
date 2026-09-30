@extends('back.layout')

@section('content')
<div class="min-vh-100 d-flex justify-content-center align-items-center" ">
    <div class="container py-5">
        <div class="card shadow-lg border-0 mx-auto" style="max-width: 900px; width: 100%; border-radius: 1rem;">

            <!-- Header avec gradient vert -->
            <div class="card-header" style="background: linear-gradient(135deg, #22a065ff, #2e7d32); color: white; border-radius: 1rem 1rem 0 0;">
                <h3 class="fw-bold mb-0 text-center">
                    <i class="bi bi-pencil-square"></i> Edit Waste Category
                </h3>
            </div>

            <div class="card-body p-5">
                <!-- Alerts -->
                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Formulaire Edit -->
                <form action="{{ route('waste_categories.update', $category->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Category Name</label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name', $category->name) }}" 
                            class="form-control" 
                            placeholder="Ex: Plastic, Metal, Organic" 
                            required
                        >
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea 
                            name="description" 
                            id="description" 
                            rows="3" 
                            class="form-control" 
                            placeholder="Enter a short description..." 
                            required>{{ old('description', $category->description) }}</textarea>
                    </div>

                    <!-- Recycling Instructions -->
                    <div class="mb-3">
                        <label for="recycling_instructions" class="form-label fw-semibold">Recycling Instructions</label>
                        <textarea 
                            name="recycling_instructions" 
                            id="recycling_instructions" 
                            rows="3" 
                            class="form-control" 
                            placeholder="How to recycle this waste type..." 
                            required>{{ old('recycling_instructions', $category->recycling_instructions) }}</textarea>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="d-flex justify-content-end mt-4 gap-2">
                        <a href="{{ route('waste_categories.index') }}" class="btn btn-secondary px-4">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-save me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
