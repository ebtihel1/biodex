{{-- resources/views/front/reservations/show.blade.php --}}
@extends('front.layout')

@section('content')
<style>
    :root {
        --bd-primary: #10b981;
        --bd-primary-dark: #059669;
        --bd-primary-darker: #047857;
        --bd-accent: #06b6d4;
        --bd-dark: #0f172a;
        --bd-gray: #64748b;
        --bd-border: #e2e8f0;
        --bd-radius: 20px;
        --bd-shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.06);
        --bd-shadow-md: 0 10px 30px -8px rgba(15, 23, 42, 0.12);
        --bd-shadow-lg: 0 24px 50px -12px rgba(16, 185, 129, 0.25);
        --bd-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ===== Page shell ===== */
    .reservation-page {
        background:
            radial-gradient(circle at 10% 0%, rgba(16, 185, 129, 0.08), transparent 45%),
            radial-gradient(circle at 90% 20%, rgba(6, 182, 212, 0.07), transparent 45%),
            linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        min-height: 100vh;
        padding: 3rem 0 5rem;
    }

    /* ===== Main card ===== */
    .reservation-card {
        background: #fff;
        border-radius: 24px;
        box-shadow: var(--bd-shadow-md);
        border: 1px solid var(--bd-border);
        overflow: hidden;
        position: relative;
        animation: bdFadeInUp 0.6s ease-out;
    }
    .reservation-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--bd-primary), var(--bd-accent), var(--bd-primary));
        background-size: 200% 100%;
        animation: bdShimmer 3s linear infinite;
        z-index: 2;
    }

    /* ===== Header ===== */
    .reservation-header {
        background: linear-gradient(135deg, #064e3b 0%, var(--bd-primary-darker) 100%);
        color: #fff;
        padding: 2rem 2.25rem;
        position: relative;
        overflow: hidden;
    }
    .reservation-header::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image:
            radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.08) 0%, transparent 40%),
            radial-gradient(circle at 80% 70%, rgba(6, 182, 212, 0.15) 0%, transparent 40%);
        pointer-events: none;
    }
    .reservation-header::after {
        content: "";
        position: absolute;
        top: -60%;
        right: -10%;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.35), transparent 65%);
        pointer-events: none;
        animation: bdPulse 4s ease-in-out infinite;
    }
    .reservation-header .header-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        flex-wrap: wrap;
    }
    .reservation-header .header-icon {
        width: 64px;
        height: 64px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.7rem;
        flex-shrink: 0;
        box-shadow: 0 8px 20px -8px rgba(0, 0, 0, 0.3);
    }
    .reservation-header .header-text {
        flex: 1;
        min-width: 220px;
    }
    .reservation-header .header-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 0.35rem 0.85rem;
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 0.65rem;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }
    .reservation-header h1 {
        font-size: clamp(1.4rem, 3vw, 1.85rem);
        font-weight: 800;
        margin: 0;
        letter-spacing: -0.02em;
        line-height: 1.2;
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: wrap;
    }
    .reservation-header h1 .id-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        padding: 0.25rem 0.7rem;
        border-radius: 100px;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        color: #fff;
        vertical-align: middle;
    }
    .reservation-header h1 .id-pill i {
        font-size: 0.7rem;
        opacity: 0.9;
    }

    /* ===== Status pill dans le header ===== */
    .header-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.35rem 0.85rem;
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        margin-top: 0.75rem;
        border: 1px solid transparent;
        backdrop-filter: blur(10px);
    }
    .header-status .dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
        animation: bdPulseDot 1.8s ease-in-out infinite;
    }
    .header-status.status-active {
        background: rgba(16, 185, 129, 0.2);
        color: #d1fae5;
        border-color: rgba(110, 231, 183, 0.4);
    }
    .header-status.status-expired {
        background: rgba(245, 158, 11, 0.2);
        color: #fef3c7;
        border-color: rgba(253, 224, 71, 0.4);
    }
    .header-status.status-cancelled {
        background: rgba(239, 68, 68, 0.2);
        color: #fecaca;
        border-color: rgba(252, 165, 165, 0.4);
    }
    .header-status.status-default {
        background: rgba(148, 163, 184, 0.2);
        color: #e2e8f0;
        border-color: rgba(203, 213, 225, 0.4);
    }

    /* ===== Body ===== */
    .reservation-body {
        padding: 2rem 2.25rem 2.25rem;
    }

    /* ===== Section titles ===== */
    .form-section-title {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--bd-dark);
        margin: 0 0 1.1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--bd-border);
        letter-spacing: -0.01em;
    }
    .form-section-title .section-icon {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--bd-primary), var(--bd-primary-dark));
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        box-shadow: 0 6px 14px -4px rgba(16, 185, 129, 0.5);
        flex-shrink: 0;
    }

    /* ===== Info items ===== */
    .info-item {
        background: #f8fafc;
        border: 1px solid var(--bd-border);
        border-radius: 14px;
        padding: 1rem 1.15rem;
        transition: var(--bd-transition);
        position: relative;
        overflow: hidden;
        height: 100%;
    }
    .info-item::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, var(--bd-primary), var(--bd-accent));
        transform: scaleY(0);
        transform-origin: top;
        transition: transform 0.35s ease;
    }
    .info-item:hover::before {
        transform: scaleY(1);
    }
    .info-item:hover {
        background: #f0fdf7;
        border-color: rgba(16, 185, 129, 0.25);
        transform: translateY(-2px);
        box-shadow: var(--bd-shadow-sm);
    }

    .info-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--bd-gray);
        margin-bottom: 0.6rem;
    }
    .info-label .label-icon {
        width: 22px;
        height: 22px;
        border-radius: 7px;
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: var(--bd-primary-dark);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
        flex-shrink: 0;
    }
    .info-value {
        font-size: 1rem;
        font-weight: 600;
        color: var(--bd-dark);
        margin: 0;
        line-height: 1.5;
        word-break: break-word;
    }
    .info-value.muted {
        color: var(--bd-gray);
        font-weight: 500;
        font-style: italic;
    }
    .info-value.highlight {
        color: var(--bd-primary-darker);
        font-weight: 700;
    }

    /* ===== Status badge dans le body ===== */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.4rem 0.9rem;
        border-radius: 100px;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.01em;
        text-transform: capitalize;
        border: 1px solid transparent;
    }
    .status-badge .dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
        animation: bdPulseDot 1.8s ease-in-out infinite;
    }
    .status-badge.active {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
        border-color: rgba(16, 185, 129, 0.3);
    }
    .status-badge.expired {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
        border-color: rgba(245, 158, 11, 0.3);
    }
    .status-badge.cancelled {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
        border-color: rgba(239, 68, 68, 0.3);
    }
    .status-badge.default {
        background: #f1f5f9;
        color: #475569;
        border-color: var(--bd-border);
    }

    /* ===== Alert ===== */
    .alert-modern {
        border-radius: 14px;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        font-size: 0.92rem;
        font-weight: 500;
        margin-bottom: 1.5rem;
        animation: bdFadeInUp 0.4s ease-out;
        border: 1px solid transparent;
    }
    .alert-modern i {
        font-size: 1.15rem;
        flex-shrink: 0;
        margin-top: 1px;
    }
    .alert-modern.alert-success {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border-color: rgba(16, 185, 129, 0.3);
        border-left: 4px solid var(--bd-primary);
        color: var(--bd-primary-darker);
    }
    .alert-modern.alert-success i { color: var(--bd-primary); }

    /* ===== Timeline visual ===== */
    .timeline-visual {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.9rem 1.1rem;
        background:
            radial-gradient(circle at 100% 0%, rgba(6, 182, 212, 0.08), transparent 50%),
            linear-gradient(135deg, #f0f9ff, #e0f2fe);
        border: 1px solid rgba(6, 182, 212, 0.2);
        border-radius: 14px;
        margin-top: 1rem;
    }
    .timeline-visual .tl-icon {
        width: 36px;
        height: 36px;
        border-radius: 11px;
        background: linear-gradient(135deg, var(--bd-accent), #0891b2);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
        box-shadow: 0 6px 14px -4px rgba(6, 182, 212, 0.5);
    }
    .timeline-visual .tl-text {
        font-size: 0.88rem;
        color: #0369a1;
        font-weight: 500;
        line-height: 1.5;
    }
    .timeline-visual .tl-text strong {
        color: #0c4a6e;
        font-weight: 700;
    }

    /* ===== Actions ===== */
    .form-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-top: 2rem;
        padding-top: 1.75rem;
        border-top: 1px solid var(--bd-border);
    }
    .btn-modern {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.85rem 1.6rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        border: none;
        transition: var(--bd-transition);
        text-decoration: none;
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }
    .btn-modern i {
        transition: transform 0.3s ease;
    }

    .btn-modern.btn-primary {
        background: linear-gradient(135deg, var(--bd-primary), var(--bd-primary-dark));
        color: #fff;
        box-shadow: 0 8px 20px -6px rgba(16, 185, 129, 0.5);
    }
    .btn-modern.btn-primary::before {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
        transition: left 0.5s ease;
    }
    .btn-modern.btn-primary:hover::before { left: 100%; }
    .btn-modern.btn-primary:hover {
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 12px 26px -8px rgba(16, 185, 129, 0.65);
    }

    .btn-modern.btn-secondary {
        background: #fff;
        color: var(--bd-gray);
        border: 1px solid var(--bd-border);
    }
    .btn-modern.btn-secondary:hover {
        background: #f8fafc;
        color: var(--bd-dark);
        border-color: #cbd5e1;
        transform: translateY(-2px);
    }
    .btn-modern.btn-secondary:hover i {
        transform: translateX(-4px);
    }

    .btn-modern.btn-danger {
        background: #fff;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    .btn-modern.btn-danger:hover {
        background: #fef2f2;
        color: #991b1b;
        border-color: #fca5a5;
        transform: translateY(-2px);
        box-shadow: 0 8px 18px -6px rgba(220, 38, 38, 0.25);
    }

    /* ===== Animations ===== */
    @keyframes bdFadeInUp {
        from { opacity: 0; transform: translateY(24px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes bdShimmer {
        0%   { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    @keyframes bdPulse {
        0%, 100% { transform: scale(1); opacity: 0.8; }
        50%      { transform: scale(1.15); opacity: 1; }
    }
    @keyframes bdPulseDot {
        0%, 100% { box-shadow: 0 0 0 0 currentColor; opacity: 1; }
        50%      { box-shadow: 0 0 0 5px transparent; opacity: 0.7; }
    }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
        .reservation-page { padding: 1.75rem 0 3.5rem; }
        .reservation-header { padding: 1.5rem 1.25rem; }
        .reservation-header .header-icon { width: 52px; height: 52px; font-size: 1.4rem; }
        .reservation-header h1 { font-size: 1.35rem; }
        .reservation-body { padding: 1.5rem 1.25rem 1.75rem; }
        .form-actions { flex-direction: column; }
        .btn-modern { width: 100%; }
    }
</style>

@php
    $statusValue = $reservation->status->value ?? $reservation->status;
    $statusClass = match($statusValue) {
        'active'    => 'active',
        'expired'   => 'expired',
        'cancelled' => 'cancelled',
        default     => 'default',
    };
    $headerStatusClass = match($statusValue) {
        'active'    => 'status-active',
        'expired'   => 'status-expired',
        'cancelled' => 'status-cancelled',
        default     => 'status-default',
    };
@endphp

<div class="reservation-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-9">
                <div class="reservation-card">

                    {{-- ============================================================
                         HEADER
                         ============================================================ --}}
                    <div class="reservation-header">
                        <div class="header-content">
                            <div class="header-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="header-text">
                                <span class="header-badge">
                                    <i class="fas fa-eye"></i> Reservation Details
                                </span>
                                <h1>
                                    Reservation
                                    <span class="id-pill">
                                        <i class="fas fa-hashtag"></i>
                                        {{ $reservation->id }}
                                    </span>
                                </h1>
                                <span class="header-status {{ $headerStatusClass }}">
                                    <span class="dot"></span>
                                    {{ ucfirst($statusValue) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- ============================================================
                         BODY
                         ============================================================ --}}
                    <div class="reservation-body">

                        @if (session('success'))
                            <div class="alert-modern alert-success" role="alert">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        <div class="row g-4">
                            {{-- ============ Overview ============ --}}
                            <div class="col-12 col-md-6">
                                <h3 class="form-section-title">
                                    <span class="section-icon"><i class="fas fa-info-circle"></i></span>
                                    Overview
                                </h3>

                                <div class="d-flex flex-column gap-3">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <span class="label-icon"><i class="fas fa-user"></i></span>
                                            User
                                        </div>
                                        <p class="info-value">
                                            {{ $reservation->user->name ?? 'Guest' }}
                                        </p>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">
                                            <span class="label-icon"><i class="fas fa-tag"></i></span>
                                            Product
                                        </div>
                                        <p class="info-value highlight">
                                            {{ \App\Http\Controllers\ReservationController::getProductName($reservation->product_id) }}
                                        </p>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="info-item">
                                                <div class="info-label">
                                                    <span class="label-icon"><i class="fas fa-hashtag"></i></span>
                                                    Quantity
                                                </div>
                                                <p class="info-value">{{ $reservation->quantity }}</p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="info-item">
                                                <div class="info-label">
                                                    <span class="label-icon"><i class="fas fa-flag"></i></span>
                                                    Status
                                                </div>
                                                <span class="status-badge {{ $statusClass }}">
                                                    <span class="dot"></span>
                                                    {{ ucfirst($statusValue) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ============ Timeline ============ --}}
                            <div class="col-12 col-md-6">
                                <h3 class="form-section-title">
                                    <span class="section-icon"><i class="fas fa-clock"></i></span>
                                    Timeline
                                </h3>

                                <div class="d-flex flex-column gap-3">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <span class="label-icon"><i class="fas fa-calendar-alt"></i></span>
                                            Reserved Until
                                        </div>
                                        <p class="info-value">
                                            <i class="fas fa-hourglass-half text-success me-1"></i>
                                            {{ $reservation->reserved_until->format('M d, Y H:i') }}
                                        </p>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">
                                            <span class="label-icon"><i class="fas fa-calendar-plus"></i></span>
                                            Created Date
                                        </div>
                                        <p class="info-value">
                                            <i class="fas fa-clock text-muted me-1"></i>
                                            {{ $reservation->created_at->format('M d, Y H:i') }}
                                        </p>
                                    </div>

                                    <div class="timeline-visual">
                                        <div class="tl-icon"><i class="fas fa-hourglass-half"></i></div>
                                        <div class="tl-text">
                                            <strong>Duration:</strong>
                                            {{ $reservation->created_at->diffForHumans($reservation->reserved_until, true) }}
                                            after creation
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ============ ACTIONS ============ --}}
                        <div class="form-actions">
                            <a href="{{ route($indexRoute) }}" class="btn-modern btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Back to Reservations
                            </a>
                            <button type="button" class="btn-modern btn-danger"
                                    data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash"></i>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     DELETE CONFIRMATION MODAL
     ============================================================ --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #7f1d1d, #dc2626); color: #fff; border: none; padding: 1.25rem 1.5rem;">
                <h5 class="modal-title" style="font-weight: 700;">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 1.5rem;">
                <p style="margin: 0; color: #475569; line-height: 1.6;">
                    Are you sure you want to delete this reservation?
                    <strong class="d-block mt-2" style="color: #dc2626;">This action cannot be undone.</strong>
                </p>
            </div>
            <div class="modal-footer" style="border: none; padding: 1rem 1.5rem 1.25rem;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                        style="border-radius: 10px; padding: 0.6rem 1.25rem; font-weight: 600;">
                    Cancel
                </button>
                <form action="{{ $destroyRoute }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                            style="border-radius: 10px; padding: 0.6rem 1.25rem; font-weight: 600; background: linear-gradient(135deg, #dc2626, #b91c1c); border: none;">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection