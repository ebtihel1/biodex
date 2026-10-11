{{-- resources/views/front/donations/show.blade.php --}}
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
    .donation-page {
        background:
            radial-gradient(circle at 10% 0%, rgba(16, 185, 129, 0.08), transparent 45%),
            radial-gradient(circle at 90% 20%, rgba(6, 182, 212, 0.07), transparent 45%),
            linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        min-height: 100vh;
        padding: 3rem 0 5rem;
    }

    /* ===== Main card ===== */
    .donation-card {
        background: #fff;
        border-radius: 24px;
        box-shadow: var(--bd-shadow-md);
        border: 1px solid var(--bd-border);
        overflow: hidden;
        position: relative;
        animation: bdFadeInUp 0.6s ease-out;
    }
    .donation-card::before {
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
    .donation-header {
        background: linear-gradient(135deg, #064e3b 0%, var(--bd-primary-darker) 100%);
        color: #fff;
        padding: 2rem 2.25rem;
        position: relative;
        overflow: hidden;
    }
    .donation-header::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image:
            radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.08) 0%, transparent 40%),
            radial-gradient(circle at 80% 70%, rgba(6, 182, 212, 0.15) 0%, transparent 40%);
        pointer-events: none;
    }
    .donation-header::after {
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
    .donation-header .header-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .donation-header .header-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        flex-shrink: 0;
        box-shadow: 0 8px 20px -8px rgba(0, 0, 0, 0.3);
    }
    .donation-header .header-text {
        flex: 1;
        min-width: 200px;
    }
    .donation-header .header-badge {
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
        margin-bottom: 0.6rem;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }
    .donation-header h1 {
        font-size: clamp(1.4rem, 3vw, 1.85rem);
        font-weight: 800;
        margin: 0;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }
    .donation-header h1 .sub {
        display: block;
        font-size: 0.9rem;
        font-weight: 500;
        opacity: 0.85;
        margin-top: 0.35rem;
        letter-spacing: 0;
    }

    /* ===== Alert ===== */
    .alert-success-modern {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-left: 4px solid var(--bd-primary);
        border-radius: 14px;
        color: var(--bd-primary-darker);
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 500;
        animation: bdFadeInUp 0.4s ease-out;
    }
    .alert-success-modern i {
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    /* ===== Body ===== */
    .donation-body {
        padding: 2rem 2.25rem 2.25rem;
    }

    /* ===== Info grid ===== */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    .info-item {
        background: #f8fafc;
        border: 1px solid var(--bd-border);
        border-radius: 16px;
        padding: 1.1rem 1.25rem;
        transition: var(--bd-transition);
        position: relative;
        overflow: hidden;
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
    .info-item.full-width {
        grid-column: 1 / -1;
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

    /* ===== Status badge ===== */
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
    .status-badge.available {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
        border-color: rgba(16, 185, 129, 0.3);
    }
    .status-badge.claimed {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1e40af;
        border-color: rgba(59, 130, 246, 0.3);
    }
    .status-badge.completed {
        background: linear-gradient(135deg, #cffafe, #a5f3fc);
        color: #155e75;
        border-color: rgba(6, 182, 212, 0.3);
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

    /* ===== Images gallery ===== */
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 0.85rem;
        margin-top: 0.5rem;
    }
    .gallery-item {
        border-radius: 14px;
        overflow: hidden;
        position: relative;
        aspect-ratio: 1 / 1;
        box-shadow: var(--bd-shadow-sm);
        transition: var(--bd-transition);
        cursor: pointer;
        border: 2px solid transparent;
    }
    .gallery-item:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: var(--bd-shadow-md);
        border-color: var(--bd-primary);
    }
    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .gallery-item:hover img {
        transform: scale(1.08);
    }
    .gallery-item::after {
        content: "\F52A";
        font-family: "bootstrap-icons";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(6, 78, 59, 0.7), rgba(16, 185, 129, 0.5));
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .gallery-item:hover::after {
        opacity: 1;
    }

    /* ===== Actions ===== */
    .donation-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-top: 2rem;
        padding-top: 1.75rem;
        border-top: 1px solid var(--bd-border);
    }
    .btn-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.8rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.92rem;
        background: linear-gradient(135deg, var(--bd-primary), var(--bd-primary-dark));
        color: #fff;
        border: none;
        transition: var(--bd-transition);
        text-decoration: none;
        position: relative;
        overflow: hidden;
    }
    .btn-back::before {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
        transition: left 0.5s ease;
    }
    .btn-back:hover::before {
        left: 100%;
    }
    .btn-back:hover {
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 10px 22px -8px rgba(16, 185, 129, 0.55);
    }
    .btn-back i {
        transition: transform 0.3s ease;
    }
    .btn-back:hover i {
        transform: translateX(-4px);
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
        .donation-page { padding: 1.75rem 0 3.5rem; }
        .donation-header { padding: 1.5rem 1.25rem; }
        .donation-header .header-icon { width: 50px; height: 50px; font-size: 1.3rem; }
        .donation-body { padding: 1.5rem 1.25rem 1.75rem; }
        .info-grid { grid-template-columns: 1fr; }
        .gallery-grid { grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); }
        .donation-actions { flex-direction: column; }
        .btn-back { width: 100%; }
    }
</style>

<div class="donation-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-9">
                <div class="donation-card">

                    {{-- ============================================================
                         HEADER
                         ============================================================ --}}
                    <div class="donation-header">
                        <div class="header-content">
                            <div class="header-icon">
                                <i class="fas fa-recycle"></i>
                            </div>
                            <div class="header-text">
                                <span class="header-badge">
                                    <i class="fas fa-gift"></i> Donation Details
                                </span>
                                <h1>
                                    {{ $donation->item }}
                                    
                                </h1>
                            </div>
                        </div>
                    </div>

                    {{-- ============================================================
                         BODY
                         ============================================================ --}}
                    <div class="donation-body">

                        @if (session('success'))
                            <div class="alert-success-modern mb-4" role="alert">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        <div class="info-grid">
                            {{-- User --}}
                            <div class="info-item">
                                <div class="info-label">
                                    <span class="label-icon"><i class="fas fa-user"></i></span>
                                    User
                                </div>
                                <p class="info-value">
                                    {{ $donation->user ? $donation->user->name : 'Guest' }}
                                </p>
                            </div>

                            {{-- Waste Category --}}
                            <div class="info-item">
                                <div class="info-label">
                                    <span class="label-icon"><i class="fas fa-trash-alt"></i></span>
                                    Waste Category
                                </div>
                                <p class="info-value">
                                    {{ $donation->waste->category->name ?? 'N/A' }}
                                </p>
                            </div>

                            {{-- Item Name --}}
                            <div class="info-item">
                                <div class="info-label">
                                    <span class="label-icon"><i class="fas fa-tag"></i></span>
                                    Item Name
                                </div>
                                <p class="info-value highlight">{{ $donation->item_name }}</p>
                            </div>

                            {{-- Condition --}}
                            <div class="info-item">
                                <div class="info-label">
                                    <span class="label-icon"><i class="fas fa-info-circle"></i></span>
                                    Condition
                                </div>
                                <p class="info-value">{{ ucfirst($donation->condition) }}</p>
                            </div>

                            {{-- Status --}}
                            <div class="info-item">
                                <div class="info-label">
                                    <span class="label-icon"><i class="fas fa-exclamation-circle"></i></span>
                                    Status
                                </div>
                                @php
                                    $statusValue = $donation->status->value ?? $donation->status;
                                    $statusClass = match($statusValue) {
                                        'available' => 'available',
                                        'claimed'   => 'claimed',
                                        'completed' => 'completed',
                                        'cancelled' => 'cancelled',
                                        default     => 'default',
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    <span class="dot"></span>
                                    {{ ucfirst($statusValue) }}
                                </span>
                            </div>

                            {{-- Pickup --}}
                            <div class="info-item">
                                <div class="info-label">
                                    <span class="label-icon"><i class="fas fa-truck"></i></span>
                                    Pickup
                                </div>
                                <p class="info-value {{ !$donation->pickup_required ? 'muted' : '' }}">
                                    @if($donation->pickup_required)
                                        <i class="fas fa-check-circle text-success me-1"></i>
                                        Yes — {{ $donation->pickup_address ?? 'N/A' }}
                                    @else
                                        <i class="fas fa-times-circle text-muted me-1"></i>
                                        Not required
                                    @endif
                                </p>
                            </div>

                            {{-- Description --}}
                            <div class="info-item full-width">
                                <div class="info-label">
                                    <span class="label-icon"><i class="fas fa-align-left"></i></span>
                                    Description
                                </div>
                                <p class="info-value {{ !$donation->description ? 'muted' : '' }}">
                                    {{ $donation->description ?? 'No description provided' }}
                                </p>
                            </div>

                            {{-- Images --}}
                            @if($donation->images && is_array($donation->images) && count($donation->images) > 0)
                                <div class="info-item full-width">
                                    <div class="info-label">
                                        <span class="label-icon"><i class="fas fa-images"></i></span>
                                        Images ({{ count($donation->images) }})
                                    </div>
                                    <div class="gallery-grid">
                                        @foreach($donation->images as $imagePath)
                                            <a href="{{ Storage::url($imagePath) }}" target="_blank" class="gallery-item">
                                                <img src="{{ Storage::url($imagePath) }}" alt="Donation Image">
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- ============================================================
                             ACTIONS
                             ============================================================ --}}
                        <div class="donation-actions">
                            <a href="{{ route($indexRoute) }}" class="btn-back">
                                <i class="fas fa-arrow-left"></i>
                                Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection