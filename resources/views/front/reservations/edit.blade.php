{{-- resources/views/front/reservations/edit.blade.php --}}
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
        transform: rotate(-10deg);
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

    /* ============================================================
       NOUVEAU : Meta chip dans le header
       ============================================================ */
    .header-meta {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        padding: 0.35rem 0.75rem;
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 0.75rem;
        color: rgba(255, 255, 255, 0.95);
    }
    .header-meta .meta-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #6ee7b7;
        box-shadow: 0 0 0 3px rgba(110, 231, 183, 0.25);
        animation: bdPulseDot 1.8s ease-in-out infinite;
    }

    /* ============================================================
       NOUVEAU : Info banner dans le form
       ============================================================ */
    .info-banner {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 0.9rem 1.1rem;
        background:
            radial-gradient(circle at 100% 0%, rgba(6, 182, 212, 0.08), transparent 50%),
            linear-gradient(135deg, #f0f9ff, #e0f2fe);
        border: 1px solid rgba(6, 182, 212, 0.2);
        border-radius: 14px;
        margin-bottom: 1.5rem;
        font-size: 0.88rem;
        color: #0369a1;
        font-weight: 500;
    }
    .info-banner .info-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--bd-accent), #0891b2);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
        box-shadow: 0 6px 14px -4px rgba(6, 182, 212, 0.5);
    }
    .info-banner strong { color: #0c4a6e; font-weight: 700; }

    /* ============================================================
       NOUVEAU : Field wrapper avec icône intégrée
       ============================================================ */
    .field-with-hint {
        position: relative;
    }
    .field-hint {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.72rem;
        color: var(--bd-gray);
        font-weight: 600;
        background: #f1f5f9;
        padding: 2px 8px;
        border-radius: 6px;
        pointer-events: none;
    }
    .form-control.has-hint,
    .form-select.has-hint {
        padding-right: 70px;
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
        0%, 100% { box-shadow: 0 0 0 0 rgba(110, 231, 183, 0.6); }
        50%      { box-shadow: 0 0 0 6px rgba(110, 231, 183, 0); }
    }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
        .reservation-page { padding: 1.75rem 0 3.5rem; }
        .reservation-header { padding: 1.5rem 1.25rem; }
        .reservation-header .header-icon { width: 52px; height: 52px; font-size: 1.4rem; }
        .reservation-header h1 { font-size: 1.35rem; }
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
                                <i class="fas fa-pen-to-square"></i>
                            </div>
                            <div class="header-text">
                                <span class="header-badge">
                                    <i class="fas fa-edit"></i> Edit Reservation
                                </span>
                                <h1>
                                    Update Reservation
                                    <span class="id-pill">
                                        <i class="fas fa-hashtag"></i>
                                        {{ $reservation->id }}
                                    </span>
                                </h1>
                                <div class="header-meta">
                                    <span class="meta-dot"></span>
                                    Editing in progress
                                </div>
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

                        {{-- Info banner --}}
                        <div class="info-banner">
                            <div class="info-icon"><i class="fas fa-lightbulb"></i></div>
                            <div>
                                Modify the fields below and click <strong>Update Reservation</strong> to save your changes.
                            </div>
                        </div>

                        <form action="{{ $updateRoute ?? route('front.reservations.update', $reservation) }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')

                            {{-- ============ SECTION 1: User & Product ============ --}}
                            <h3 class="form-section-title">
                                <span class="section-icon"><i class="fas fa-users"></i></span>
                                User & Product
                            </h3>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="user_id" class="form-label">
                                        <i class="fas fa-user"></i>User
                                    </label>
                                    <select name="user_id" id="user_id" class="form-select" required>
                                        <option value="">Select User</option>
                                        @foreach(\App\Models\User::all() as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id', $reservation->user_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="product_id" class="form-label">
                                        <i class="fas fa-tag"></i>Product
                                    </label>
                                    <select name="product_id" id="product_id" class="form-select" required>
                                        <option value="">Select Product</option>
                                        @if(isset($products) && !empty($products))
                                            @foreach($products as $id => $product)
                                                <option value="{{ $id }}" {{ old('product_id', $reservation->product_id) == $id ? 'selected' : '' }}>
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
                            </div>

                            {{-- ============ SECTION 2: Reservation Details ============ --}}
                            <h3 class="form-section-title">
                                <span class="section-icon"><i class="fas fa-clock"></i></span>
                                Reservation Details
                            </h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="quantity" class="form-label">
                                        <i class="fas fa-hashtag"></i>Quantity
                                    </label>
                                    <div class="field-with-hint">
                                        <input type="number" name="quantity" id="quantity"
                                               value="{{ old('quantity', $reservation->quantity) }}" min="1"
                                               class="form-control has-hint" required>
                                        <span class="field-hint">min. 1</span>
                                    </div>
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="reserved_until" class="form-label">
                                        <i class="fas fa-calendar-alt"></i>Reserved Until
                                    </label>
                                    <input type="datetime-local" name="reserved_until" id="reserved_until"
                                           value="{{ old('reserved_until', $reservation->reserved_until->format('Y-m-d\TH:i')) }}"
                                           class="form-control" required>
                                    @error('reserved_until')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="status" class="form-label">
                                        <i class="fas fa-info-circle"></i>Status
                                    </label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="">Select Status</option>
                                        @foreach(\App\Enums\ReservationStatus::cases() as $status)
                                            <option value="{{ $status->value }}" {{ old('status', $reservation->status) == $status->value ? 'selected' : '' }}>
                                                {{ ucfirst($status->value) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- ============ ACTIONS ============ --}}
                            <div class="form-actions">
                                <a href="{{ $showRoute ?? route('front.reservations.show', $reservation) }}" class="btn-modern btn-secondary">
                                    <i class="fas fa-arrow-left"></i>
                                    Cancel
                                </a>
                                <button type="submit" class="btn-modern btn-primary">
                                    <i class="fas fa-save"></i>
                                    Update Reservation
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
});
</script>
@endsection