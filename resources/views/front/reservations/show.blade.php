{{-- resources/views/front/reservations/show.blade.php --}}
@extends('front.layout')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center bg-gradient text-dark" style="background: linear-gradient(to bottom right, #d4edda, #c3e6cb);">
    <div class="container py-3 py-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-10">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-3 p-md-4">
                        <h1 class="h3 fw-bold mb-4 text-success text-center">
                            <i class="fas fa-calendar-check me-2"></i>Reservation Details #{{ $reservation->id }}
                        </h1>
                        
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <h6 class="fw-bold text-success mb-3">
                                    <i class="fas fa-info-circle me-1 text-muted"></i>Overview
                                </h6>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-user me-1 text-muted"></i>User
                                    </label>
                                    <p class="form-control">{{ $reservation->user->name ?? 'Guest' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-tag me-1 text-muted"></i>Product
                                    </label>
                                    <p class="form-control">{{ \App\Http\Controllers\ReservationController::getProductName($reservation->product_id) }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-hashtag me-1 text-muted"></i>Quantity
                                    </label>
                                    <p class="form-control">{{ $reservation->quantity }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-exclamation-circle me-1 text-muted"></i>Status
                                    </label>
                                    <p class="form-control">
                                        <span class="badge @switch($reservation->status->value)
                                            @case('active') bg-success @break
                                            @case('expired') bg-warning @break
                                            @case('cancelled') bg-danger @break
                                            @default bg-secondary
                                        @endswitch">
                                            {{ $reservation->status->value }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <h6 class="fw-bold text-success mb-3">
                                    <i class="fas fa-clock me-1 text-muted"></i>Timeline
                                </h6>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-calendar-alt me-1 text-muted"></i>Reserved Until
                                    </label>
                                    <p class="form-control">{{ $reservation->reserved_until->format('M d, Y H:i') }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-calendar me-1 text-muted"></i>Created Date
                                    </label>
                                    <p class="form-control">{{ $reservation->created_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-4">
                            <a href="{{ route($indexRoute) }}" class="btn btn-secondary w-100 w-md-auto">
                                <i class="fas fa-arrow-left me-2"></i>Back to Reservations
                            </a>
                            <div class="d-flex gap-2 w-100 w-md-auto">
                                <form action="{{ $destroyRoute }}" method="POST" class="d-inline w-100">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hover & Responsive Fluidity */
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12) !important;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }

    @media (max-width: 768px) {
        .row > .col-md-6 {
            margin-bottom: 2rem;
        }
        .space-y-4 > * { 
            margin-bottom: 1rem !important; 
        }
        .d-flex.gap-2 { 
            flex-direction: column; 
        }
        .btn { 
            width: 100%; 
            margin-bottom: 0.5rem; 
        }
    }

    /* Custom badge styles */
    .badge {
        font-size: 0.75rem;
        padding: 0.4em 0.7em;
        border-radius: 0.375rem;
    }
</style>
@endsection