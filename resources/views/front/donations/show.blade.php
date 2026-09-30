{{-- resources/views/front/donations/show.blade.php --}}
@extends('front.layout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card shadow">
                <div class="card-body p-3 p-md-4">
                    <h1 class="h3 font-weight-bold mb-4 text-success text-center">
                        <i class="fas fa-recycle me-2"></i>Donation #{{ $donation->id }}
                    </h1>
                    
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user me-1 text-muted"></i>User
                            </label>
                            <p class="form-control">{{ $donation->user ? $donation->user->name : 'Guest' }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-trash-alt me-1 text-muted"></i>Waste Category
                            </label>
                            <p class="form-control">{{ $donation->waste->category->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-tag me-1 text-muted"></i>Item Name
                            </label>
                            <p class="form-control fw-bold">{{ $donation->item_name }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-info-circle me-1 text-muted"></i>Condition
                            </label>
                            <p class="form-control">{{ ucfirst($donation->condition) }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-exclamation-circle me-1 text-muted"></i>Status
                            </label>
                            <p class="form-control">
                                <span class="badge @switch($donation->status->value ?? $donation->status)
                                    @case('available') bg-success @break
                                    @case('claimed') bg-primary @break
                                    @case('completed') bg-info @break
                                    @case('cancelled') bg-danger @break
                                    @default bg-secondary
                                @endswitch">
                                    {{ $donation->status->value ?? $donation->status }}
                                </span>
                            </p>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">
                                <i class="fas fa-align-left me-1 text-muted"></i>Description
                            </label>
                            <p class="form-control">{{ $donation->description ?? 'N/A' }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-truck me-1 text-muted"></i>Pickup
                            </label>
                            <p class="form-control">{{ $donation->pickup_required ? 'Yes: ' . ($donation->pickup_address ?? 'N/A') : 'No' }}</p>
                        </div>
                        @if($donation->images && is_array($donation->images) && count($donation->images) > 0)
                            <div class="col-12">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-images me-1 text-muted"></i>Images
                                </label>
                                <div class="row g-2">
                                    @foreach($donation->images as $imagePath)
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <img src="{{ Storage::url($imagePath) }}" alt="Donation Image" class="img-fluid rounded shadow-sm w-100" style="height: 150px; object-fit: cover;">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mt-4">
                        <a href="{{ route($indexRoute) }}" class="btn btn-secondary w-100 w-md-auto">
                            <i class="fas fa-arrow-left me-1"></i>Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
