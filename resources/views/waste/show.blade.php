@extends('back.layout')

@section('content')
<div class="min-vh-100 d-flex justify-content-center align-items-center" style="background-color: #f8f9fa;">
    <div class="container py-5">
        <div class="card shadow-lg border-0 mx-auto" style="max-width: 1200px; width: 100%; border-radius: 1rem;">
            <div class="card-header bg-success text-white text-center py-4" style="border-radius: 1rem 1rem 0 0;">
                <h2 class="fw-bold mb-0">Waste Details ♻️</h2>
            </div>

            <div class="card-body p-5">
                <!-- Image Section -->
                @if ($waste->image_path)
                    <div class="text-center mb-4">
                        <img src="{{ asset('storage/' . $waste->image_path) }}" 
                             alt="Waste Image" 
                             class="img-fluid rounded shadow-sm" 
                             style="max-height: 250px;">
                    </div>
                @else
                    <div class="text-center mb-4 text-muted fst-italic">
                        No image available
                    </div>
                @endif

                <!-- Waste Information -->
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="border p-3 rounded bg-light">
                            <p class="fw-semibold text-secondary mb-1">Collection Point</p>
                            <p class="mb-0">{{ $waste->collectionPoint->name ?? 'Unknown Collection Point' }}</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border p-3 rounded bg-light">
                            <p class="fw-semibold text-secondary mb-1">Type</p>
                            <p class="mb-0">{{ $waste->type }}</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border p-3 rounded bg-light">
                            <p class="fw-semibold text-secondary mb-1">Weight</p>
                            <p class="mb-0">{{ $waste->weight }} kg</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border p-3 rounded bg-light">
                            <p class="fw-semibold text-secondary mb-1">Status</p>
                            <p class="mb-0 text-capitalize">{{ $waste->status }}</p>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="border p-3 rounded bg-light">
                            <p class="fw-semibold text-secondary mb-1">Description</p>
                            <p class="mb-0">{{ $waste->description ?? 'No description provided.' }}</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border p-3 rounded bg-light">
                            <p class="fw-semibold text-secondary mb-1">Category</p>
                            <p class="mb-0">{{ $waste->category->name ?? 'Unknown' }}</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border p-3 rounded bg-light">
                            <p class="fw-semibold text-secondary mb-1">User</p>
                            <p class="mb-0">{{ $waste->user->name ?? 'Unknown User' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Button -->
                <div class="text-center mt-5">
                    <a href="{{ route('wastes.index') }}" class="btn btn-success px-4">
                        <i class="bi bi-arrow-left-circle me-1"></i> Back to Waste List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
