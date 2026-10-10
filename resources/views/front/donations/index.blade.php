{{-- resources/views/front/donations/index.blade.php --}}
@extends('front.layout')

@section('content')
<div class="container py-4">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-success animate-fade-in">My Donations</h1>
        <p class="lead">Track and manage your recycling donations.</p>
    </div>

    {{-- CTA --}}
    <div class="text-center mb-4">
        <a href="{{ route('front.donations.create') }}"
           class="btn btn-success btn-lg shadow-lg animate-pulse"
           style="background-color:#10b981;border-color:#10b981;color:#fff;font-weight:bold;padding:12px 30px;transition:all .3s ease;">
            <i class="fas fa-plus-circle me-2"></i>Make New Donation
        </a>
    </div>

    {{-- Filtres --}}
    <div class="row justify-content-center mb-4">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm animate-slide-up">
                <div class="card-body">
                    <form method="GET" action="{{ route('front.donations.index') }}" class="row g-3">
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
                            <label for="item_search" class="form-label fw-semibold">Item Search</label>
                            <input type="text" name="item_search" id="item_search" class="form-control"
                                   value="{{ request('item_search') }}" placeholder="Search items...">
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex flex-column flex-md-row gap-2 h-100 align-items-start align-items-md-end justify-content-md-end">
                                <button type="submit" class="btn btn-outline-success w-100 w-md-auto">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                                <a href="{{ route('front.donations.index') }}" class="btn btn-secondary w-100 w-md-auto">
                                    <i class="fas fa-refresh me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Success --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show animate-slide-up" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Compteur résultats --}}
    @if(!$donations->isEmpty())
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <p class="text-muted mb-0">
                <i class="bi bi-grid-3x3-gap me-1"></i>
                {{ $donations->total() }} donation(s) trouvée(s)
            </p>
            <small class="text-muted">
                Affichage {{ $donations->firstItem() }}–{{ $donations->lastItem() }}
                sur {{ $donations->total() }}
            </small>
        </div>
    @endif

    {{-- Grille de dons --}}
    <div class="row g-3 g-md-4">
        @if($donations->isEmpty())
            <div class="col-12">
                <div class="card shadow animate-slide-up">
                    <div class="card-body text-center py-5">
                        <div class="text-muted mb-3">
                            <i class="fas fa-recycle fa-3x mb-3"></i>
                        </div>
                        <h5 class="card-title text-muted mb-3">No Donations Found</h5>
                        <p class="card-text text-muted mb-4">You haven't made any donations yet.</p>
                        <a href="{{ route('front.donations.create') }}" class="btn btn-success">
                            <i class="fas fa-plus-circle me-2"></i>Create Your First Donation
                        </a>
                    </div>
                </div>
            </div>
        @else
            @foreach($donations as $index => $donation)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card shadow h-100 border-light donation-card animate-fade-in-up stagger-{{ ($index % 12) + 1 }}"
                         style="transition: transform .3s ease, box-shadow .3s ease;">

                        {{-- Status --}}
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge @switch($donation->status->value ?? $donation->status)
                                @case('available') bg-success @break
                                @case('claimed') bg-primary @break
                                @case('completed') bg-info @break
                                @case('cancelled') bg-danger @break
                                @default bg-secondary
                            @endswitch">
                                {{ $donation->status->value ?? $donation->status }}
                            </span>
                        </div>

                        {{-- Image --}}
                        <div class="card-img-top bg-light text-success d-flex align-items-center justify-content-center overflow-hidden"
                             style="height:140px;border-bottom:1px solid #e9ecef;">
                            @if($donation->images && is_array($donation->images) && count($donation->images) > 0)
                                <img src="{{ Storage::url($donation->images[0]) }}"
                                     alt="Donation Image"
                                     class="img-fluid rounded object-fit-cover w-100 h-100"
                                     style="object-fit: cover;">
                            @else
                                <i class="fas fa-recycle fa-3x"></i>
                            @endif
                        </div>

                        <div class="card-body d-flex flex-column">
                            {{-- Nom du donateur --}}
                            <h6 class="card-title text-dark mb-3">
                                <i class="fas fa-user-circle text-success me-1"></i>
                                {{ $donation->user->name ?? 'Anonymous' }}
                            </h6>

                            <div class="donation-details flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">Waste Category:</small>
                                    <span class="fw-semibold text-end">{{ $donation->waste->category->name ?? 'N/A' }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">Item:</small>
                                    <span class="fw-semibold">{{ Str::limit($donation->item_name, 20) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">Condition:</small>
                                    <span class="fw-semibold">{{ ucfirst($donation->condition) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <small class="text-muted">Date:</small>
                                    <small class="text-muted">{{ $donation->created_at->format('M d, Y') }}</small>
                                </div>
                            </div>

                            <div class="mt-auto pt-2">
                                <a href="{{ route('front.donations.show', $donation) }}"
                                   class="btn btn-outline-success btn-sm w-100">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- ============ PAGINATION ============ --}}
    @if($donations->hasPages())
        <div class="d-flex justify-content-center mt-5">
            <nav aria-label="Donations pagination">
                <ul class="pagination pagination-modern shadow-sm">
                    {{-- Previous --}}
                    @if ($donations->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="fas fa-chevron-left me-1"></i>Previous
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $donations->previousPageUrl() }}">
                                <i class="fas fa-chevron-left me-1"></i>Previous
                            </a>
                        </li>
                    @endif

                    {{-- Pages --}}
                    @foreach ($donations->getUrlRange(1, $donations->lastPage()) as $page => $url)
                        @if ($page == $donations->currentPage())
                            <li class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if ($donations->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $donations->nextPageUrl() }}">
                                Next<i class="fas fa-chevron-right ms-1"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">
                                Next<i class="fas fa-chevron-right ms-1"></i>
                            </span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    @endif
</div>

<style>
    /* ===== Animations ===== */
    @keyframes fadeInUpStagger {
        from { opacity: 0; transform: translateY(30px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .stagger-1  { animation: fadeInUpStagger .5s ease-out .1s both; }
    .stagger-2  { animation: fadeInUpStagger .5s ease-out .2s both; }
    .stagger-3  { animation: fadeInUpStagger .5s ease-out .3s both; }
    .stagger-4  { animation: fadeInUpStagger .5s ease-out .4s both; }
    .stagger-5  { animation: fadeInUpStagger .5s ease-out .5s both; }
    .stagger-6  { animation: fadeInUpStagger .5s ease-out .6s both; }
    .stagger-7  { animation: fadeInUpStagger .5s ease-out .7s both; }
    .stagger-8  { animation: fadeInUpStagger .5s ease-out .8s both; }
    .stagger-9  { animation: fadeInUpStagger .5s ease-out .9s both; }
    .stagger-10 { animation: fadeInUpStagger .5s ease-out 1s both; }
    .stagger-11 { animation: fadeInUpStagger .5s ease-out 1.1s both; }
    .stagger-12 { animation: fadeInUpStagger .5s ease-out 1.2s both; }

    .animate-fade-in    { animation: fadeIn .6s ease-in-out; }
    .animate-slide-up   { animation: slideUp .5s ease-out; }
    .animate-fade-in-up { animation: fadeInUp .6s ease-out; }
    .animate-pulse      { animation: pulse 2s infinite; }

    @keyframes fadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }
    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to   { transform: translateY(0); opacity: 1; }
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50%      { transform: scale(1.05); }
    }

    /* ===== Carte ===== */
    .donation-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, .15) !important;
        border-color: #10b981 !important;
    }

    /* ===== Pagination moderne ===== */
    .pagination-modern {
        gap: 6px;
        margin-bottom: 0;
        flex-wrap: wrap;
        justify-content: center;
    }
    .pagination-modern .page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e0e0e0;
        border-radius: 10px !important;
        color: #10b981;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 0.55rem 0.95rem;
        min-width: 42px;
        text-align: center;
        transition: all .25s ease;
        background: #fff;
        text-decoration: none;
    }
    .pagination-modern .page-link:hover {
        background: #10b981;
        color: #fff;
        border-color: #10b981;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, .25);
    }
    .pagination-modern .page-item.active .page-link {
        background: linear-gradient(135deg, #10b981, #059669);
        border-color: #10b981;
        color: #fff;
        box-shadow: 0 4px 14px rgba(16, 185, 129, .35);
    }
    .pagination-modern .page-item.disabled .page-link {
        background: #f8f9fa;
        border-color: #e9ecef;
        color: #adb5bd;
        cursor: not-allowed;
    }
    .pagination-modern .page-item:first-child .page-link,
    .pagination-modern .page-item:last-child .page-link {
        border-radius: 10px !important;
    }

    /* ===== Responsive ===== */
    @media (max-width: 576px) {
        .card-body { padding: 1rem !important; }
        .card-title { font-size: 1rem !important; }
        .donation-card .btn { padding: .5rem 1rem; font-size: .875rem; }
        .pagination-modern .page-link {
            padding: .4rem .65rem;
            font-size: .82rem;
            min-width: 36px;
        }
    }
    @media (max-width: 768px) {
        .col-md-3 { margin-bottom: 1rem; }
    }
    @media (min-width: 1200px) {
        .card-img-top { height: 160px !important; }
    }

    .badge {
        font-size: .7rem;
        padding: .35em .65em;
    }
    .object-fit-cover { object-fit: cover !important; }
</style>
@endsection