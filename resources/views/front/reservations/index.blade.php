{{-- resources/views/front/reservations/index.blade.php --}}
@extends('front.layout')

@section('content')
<div class="container py-4">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-success animate-fade-in">My Reservations</h1>
        <p class="lead">Manage your product reservations for sustainability.</p>
    </div>

    {{-- Prominent CTA Button at Top --}}
    <div class="text-center mb-4">
        <a href="{{ route('front.reservations.create') }}" class="btn btn-success btn-lg shadow-lg animate-pulse" style="background-color: #10b981; border-color: #10b981; color: white; font-weight: bold; padding: 12px 30px; transition: all 0.3s ease;">
            <i class="fas fa-plus-circle me-2"></i>Make New Reservation
        </a>
    </div>

    {{-- Search and Filter Section --}}
    <div class="row justify-content-center mb-4">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm animate-slide-up">
                <div class="card-body">
                    <form method="GET" action="{{ route('front.reservations.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="date_from" class="form-label fw-semibold">From Date</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" 
                                   value="{{ request('date_from') }}" max="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label fw-semibold">To Date</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" 
                                   value="{{ request('date_to') }}" max="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="product_search" class="form-label fw-semibold">Product Search</label>
                            <input type="text" name="product_search" id="product_search" class="form-control" 
                                   value="{{ request('product_search') }}" placeholder="Search products...">
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex flex-column flex-md-row gap-2 h-100 align-items-start align-items-md-end justify-content-md-end">
                                <button type="submit" class="btn btn-outline-success w-100 w-md-auto">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                                <a href="{{ route('front.reservations.index') }}" class="btn btn-secondary w-100 w-md-auto">
                                    <i class="fas fa-refresh me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show animate-slide-up" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Reservations Grid --}}
    <div class="row g-3 g-md-4">
        @if($reservations->isEmpty())
            <div class="col-12">
                <div class="card shadow animate-slide-up">
                    <div class="card-body text-center py-5">
                        <div class="text-muted mb-3">
                            <i class="fas fa-calendar-times fa-3x mb-3"></i>
                        </div>
                        <h5 class="card-title text-muted mb-3">No Reservations Found</h5>
                        <p class="card-text text-muted mb-4">You haven't made any reservations yet.</p>
                        <a href="{{ route('front.reservations.create') }}" class="btn btn-success">
                            <i class="fas fa-plus-circle me-2"></i>Create Your First Reservation
                        </a>
                    </div>
                </div>
            </div>
        @else
            @foreach($reservations as $index => $reservation)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card shadow h-100 border-light reservation-card animate-fade-in-up stagger-{{ ($index % 12) + 1 }}" 
                         style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    
                        {{-- Reservation Status Badge --}}
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge @switch($reservation->status->value)
                                @case('active') bg-success @break
                                @case('expired') bg-warning @break
                                @case('cancelled') bg-danger @break
                                @default bg-secondary
                            @endswitch">
                                {{ $reservation->status->value }}
                            </span>
                        </div>

                        {{-- Reservation Image/Icon --}}
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light text-success" 
                             style="height: 140px; border-bottom: 1px solid #e9ecef;">
                            <i class="fas fa-calendar-check fa-3x"></i>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title text-dark mb-3">
                                <i class="fas fa-hashtag text-muted me-1"></i>Reservation #{{ $reservation->id }}
                            </h6>
                            
                            <div class="reservation-details flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">Product:</small>
                                    <span class="fw-semibold text-end">{{ \App\Http\Controllers\ReservationController::getProductName($reservation->product_id) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">Quantity:</small>
                                    <span class="fw-semibold">{{ $reservation->quantity }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">Until:</small>
                                    <span class="fw-semibold">{{ $reservation->reserved_until->format('M d, Y') }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <small class="text-muted">Date:</small>
                                    <small class="text-muted">{{ $reservation->created_at->format('M d, Y') }}</small>
                                </div>
                            </div>

                            <div class="mt-auto pt-2">
                                <a href="{{ route('front.reservations.show', $reservation) }}" class="btn btn-outline-success btn-sm w-100">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- Pagination --}}
    @if($reservations->hasPages())
    <div class="row justify-content-center mt-5">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                <nav aria-label="Page navigation">
                    <ul class="pagination shadow-sm">
                        {{-- Previous Page Link --}}
                        @if ($reservations->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link" style="background-color: #f8f9fa; border-color: #dee2e6; color: #6c757d; padding: 10px 20px;">
                                    <i class="fas fa-chevron-left me-1"></i>Previous
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $reservations->previousPageUrl() }}" 
                                   style="background-color: #10b981; border-color: #10b981; color: white; font-weight: bold; padding: 10px 20px; transition: all 0.3s ease;">
                                    <i class="fas fa-chevron-left me-1"></i>Previous
                                </a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($reservations->getUrlRange(1, $reservations->lastPage()) as $page => $url)
                            @if ($page == $reservations->currentPage())
                                <li class="page-item active">
                                    <span class="page-link" style="background-color: #10b981; border-color: #10b981; font-weight: bold; padding: 10px 15px;">
                                        {{ $page }}
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}" 
                                       style="background-color: white; border-color: #10b981; color: #10b981; font-weight: bold; padding: 10px 15px; transition: all 0.3s ease;">
                                        {{ $page }}
                                    </a>
                                </li>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($reservations->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $reservations->nextPageUrl() }}" 
                                   style="background-color: #10b981; border-color: #10b981; color: white; font-weight: bold; padding: 10px 20px; transition: all 0.3s ease;">
                                    Next<i class="fas fa-chevron-right ms-1"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link" style="background-color: #f8f9fa; border-color: #dee2e6; color: #6c757d; padding: 10px 20px;">
                                    Next<i class="fas fa-chevron-right ms-1"></i>
                                </span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    /* Staggered Card Animations */
    @keyframes fadeInUpStagger {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Stagger delays for cards */
    .stagger-1 { animation: fadeInUpStagger 0.5s ease-out 0.1s both; }
    .stagger-2 { animation: fadeInUpStagger 0.5s ease-out 0.2s both; }
    .stagger-3 { animation: fadeInUpStagger 0.5s ease-out 0.3s both; }
    .stagger-4 { animation: fadeInUpStagger 0.5s ease-out 0.4s both; }
    .stagger-5 { animation: fadeInUpStagger 0.5s ease-out 0.5s both; }
    .stagger-6 { animation: fadeInUpStagger 0.5s ease-out 0.6s both; }
    .stagger-7 { animation: fadeInUpStagger 0.5s ease-out 0.7s both; }
    .stagger-8 { animation: fadeInUpStagger 0.5s ease-out 0.8s both; }
    .stagger-9 { animation: fadeInUpStagger 0.5s ease-out 0.9s both; }
    .stagger-10 { animation: fadeInUpStagger 0.5s ease-out 1.0s both; }
    .stagger-11 { animation: fadeInUpStagger 0.5s ease-out 1.1s both; }
    .stagger-12 { animation: fadeInUpStagger 0.5s ease-out 1.2s both; }

    /* Card Hover Effects */
    .reservation-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.15) !important;
        border-color: #10b981 !important;
    }

    /* Pagination Hover Effects */
    .page-link:hover:not(.disabled):not(.active) {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    /* Base Animations */
    .animate-fade-in {
        animation: fadeIn 0.6s ease-in-out;
    }
    
    .animate-slide-up {
        animation: slideUp 0.5s ease-out;
    }
    
    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }
    
    .animate-pulse {
        animation: pulse 2s infinite;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideUp {
        from { 
            transform: translateY(20px); 
            opacity: 0; 
        }
        to { 
            transform: translateY(0); 
            opacity: 1; 
        }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes pulse {
        0%, 100% { 
            transform: scale(1); 
        }
        50% { 
            transform: scale(1.05); 
        }
    }

    /* Responsive Design */
    @media (max-width: 576px) {
        .card-body { 
            padding: 1rem !important; 
        }
        .card-title { 
            font-size: 1rem !important; 
        }
        .reservation-card .btn { 
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        .pagination {
            flex-wrap: wrap;
            justify-content: center;
        }
        .page-link {
            padding: 8px 15px !important;
            font-size: 0.875rem;
        }
    }
    
    @media (max-width: 768px) {
        .col-md-3 {
            margin-bottom: 1rem;
        }
    }
    
    @media (min-width: 1200px) {
        .card-img-top { 
            height: 160px !important; 
        }
    }

    /* Custom badge styles */
    .badge {
        font-size: 0.7rem;
        padding: 0.35em 0.65em;
    }
</style>
@endsection