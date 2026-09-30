@extends('back.layout')

@section('content')
<div >
    <div class="container py-5" style="max-width: 900px;">

        <!-- Container principal avec gradient header -->
        <div style="background-color: white; border-radius: 1rem; box-shadow: 0 4px 20px rgba(0,0,0,0.1); overflow: hidden;">
            
            <!-- Header -->
            <div style="background: linear-gradient(135deg, #22a065ff, #2e7d32); color: white; padding: 1.5rem; text-align: center;">
                <h3 class="fw-bold mb-0">
                    <i class="bi bi-plus-circle"></i> Add New Waste Category
                </h3>
            </div>

            <!-- Body -->
            <div style="padding: 2rem;">

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

                <!-- Formulaire -->
                <form action="{{ route('waste_categories.store') }}" method="POST">
                    @csrf

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Category Name</label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name') }}" 
                            class="form-control" 
                            placeholder="Ex: Plastic, Metal, Organic" 
                            required
                            style="border-radius: 0.5rem; padding: 0.75rem;"
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
                            required
                            style="border-radius: 0.5rem; padding: 0.75rem;"
                        >{{ old('description') }}</textarea>
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
                            required
                            style="border-radius: 0.5rem; padding: 0.75rem;"
                        >{{ old('recycling_instructions') }}</textarea>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="d-flex justify-content-end mt-4 gap-2">
                        <a href="{{ route('waste_categories.index') }}" class="btn btn-secondary px-4">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-plus-circle me-1"></i> Add Category
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection
