{{-- resources/views/front/reservations/create.blade.php --}}
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
        gap: 1rem;
        flex-wrap: wrap;
    }
    .reservation-header .header-icon {
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
    .reservation-header .header-text {
        flex: 1;
        min-width: 200px;
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
        margin-bottom: 0.6rem;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }
    .reservation-header h1 {
        font-size: clamp(1.4rem, 3vw, 1.85rem);
        font-weight: 800;
        margin: 0;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }
    .reservation-header h1 .sub {
        display: block;
        font-size: 0.9rem;
        font-weight: 500;
        opacity: 0.85;
        margin-top: 0.35rem;
        letter-spacing: 0;
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
        margin: 1.75rem 0 1.1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--bd-border);
        letter-spacing: -0.01em;
    }
    .form-section-title:first-child { margin-top: 0; }
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

    /* ===== Form controls ===== */
    .form-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--bd-dark);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        letter-spacing: 0.01em;
    }
    .form-label i {
        color: var(--bd-primary);
        font-size: 0.8rem;
    }
    .form-label .optional {
        color: var(--bd-gray);
        font-weight: 500;
        font-size: 0.75rem;
        text-transform: none;
        letter-spacing: 0;
    }

    .form-control,
    .form-select {
        border: 1px solid var(--bd-border);
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        background: #fff;
        transition: var(--bd-transition);
        color: var(--bd-dark);
    }
    .form-control:focus,
    .form-select:focus {
        border-color: var(--bd-primary);
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.12);
        outline: none;
    }
    .form-control::placeholder {
        color: #94a3b8;
        font-style: italic;
    }

    /* ===== Alerts ===== */
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
    .alert-modern ul {
        margin: 0;
        padding-left: 1rem;
    }
    .alert-modern ul li { margin-bottom: 0.25rem; }
    .alert-modern ul li:last-child { margin-bottom: 0; }

    .alert-modern.alert-danger {
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
        border-color: rgba(239, 68, 68, 0.3);
        border-left: 4px solid #ef4444;
        color: #991b1b;
    }
    .alert-modern.alert-danger i { color: #dc2626; }

    .alert-modern.alert-success {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border-color: rgba(16, 185, 129, 0.3);
        border-left: 4px solid var(--bd-primary);
        color: var(--bd-primary-darker);
    }
    .alert-modern.alert-success i { color: var(--bd-primary); }

    /* ===== Actions ===== */
    .form-actions {
        display: flex;
        gap: 0.85rem;
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
    .btn-modern.btn-primary:hover i {
        transform: translateX(4px) rotate(-10deg);
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

    /* ===== Validation ===== */
    .invalid-feedback {
        font-size: 0.82rem;
        color: #dc2626;
        margin-top: 0.4rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }
    .was-validated .form-control:invalid,
    .was-validated .form-select:invalid {
        border-color: #ef4444;
        background-image: none;
    }
    .was-validated .form-control:valid,
    .was-validated .form-select:valid {
        border-color: var(--bd-primary);
        background-image: none;
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

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
        .reservation-page { padding: 1.75rem 0 3.5rem; }
        .reservation-header { padding: 1.5rem 1.25rem; }
        .reservation-header .header-icon { width: 50px; height: 50px; font-size: 1.3rem; }
        .reservation-body { padding: 1.5rem 1.25rem 1.75rem; }
        .form-actions { flex-direction: column-reverse; }
        .btn-modern { width: 100%; }
    }
</style>

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
                                    <i class="fas fa-clock"></i> New Reservation
                                </span>
                                <h1>
                                    Make a Reservation
                                    <span class="sub">Reserve your items before they're gone</span>
                                </h1>
                            </div>
                        </div>
                    </div>

                    {{-- ============================================================
                         BODY
                         ============================================================ --}}
                    <div class="reservation-body">

                        @if ($errors->any())
                            <div class="alert-modern alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle"></i>
                                <div>
                                    <strong class="d-block mb-1">Please fix the following errors:</strong>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert-modern alert-success" role="alert">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        <form action="{{ route($storeRoute) }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                            {{-- ============ SECTION 1: Product Selection ============ --}}
                            <h3 class="form-section-title">
                                <span class="section-icon"><i class="fas fa-box"></i></span>
                                Product Selection
                            </h3>

                            <div class="mb-3">
                                <label for="product_id" class="form-label">
                                    <i class="fas fa-tag"></i>Product
                                </label>
                                <select name="product_id" id="product_id" class="form-select" required>
                                    <option value="">Select a product…</option>
                                    @if(isset($products) && !empty($products))
                                        @foreach($products as $id => $product)
                                            <option value="{{ $id }}" {{ old('product_id') == $id ? 'selected' : '' }}>
                                                {{ $product['name'] }} ({{ $product['price'] }})
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>No products available</option>
                                    @endif
                                </select>
                                @error('product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="quantity" class="form-label">
                                    <i class="fas fa-hashtag"></i>Quantity
                                </label>
                                <input type="number" name="quantity" id="quantity"
                                       value="{{ old('quantity') }}" min="1"
                                       class="form-control" placeholder="Enter quantity…" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ============ SECTION 2: Reservation Window ============ --}}
                            <h3 class="form-section-title">
                                <span class="section-icon"><i class="fas fa-clock"></i></span>
                                Reservation Window
                            </h3>

                            <div class="mb-3">
                                <label for="reserved_until" class="form-label">
                                    <i class="fas fa-calendar-alt"></i>Reserved Until
                                </label>
                                <input type="datetime-local" name="reserved_until" id="reserved_until"
                                       value="{{ old('reserved_until') }}" class="form-control" required>
                                @error('reserved_until')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ============ ACTIONS ============ --}}
                            <div class="form-actions">
                                <a href="{{ route($indexRoute) }}" class="btn-modern btn-secondary">
                                    <i class="fas fa-arrow-left"></i>
                                    Cancel
                                </a>
                                <button type="submit" class="btn-modern btn-primary">
                                    <i class="fas fa-calendar-check"></i>
                                    Confirm Reservation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     SCRIPTS
     ============================================================ --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    /* ============================================================
       BOOTSTRAP VALIDATION
       ============================================================ */
    (function () {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();

    /* ============================================================
       SET MIN DATETIME TO NOW
       ============================================================ */
    const reservedUntil = document.getElementById('reserved_until');
    if (reservedUntil) {
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        reservedUntil.min = now.toISOString().slice(0, 16);
    }
});
</script>
@endsection