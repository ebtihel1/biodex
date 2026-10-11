{{-- resources/views/front/donations/create.blade.php --}}
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

    /* ===== Body ===== */
    .donation-body {
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
    textarea.form-control {
        min-height: 110px;
        resize: vertical;
        line-height: 1.6;
    }

    /* File input */
    .form-control[type="file"] {
        padding: 0.65rem 1rem;
        cursor: pointer;
    }
    .form-control[type="file"]::file-selector-button {
        background: linear-gradient(135deg, var(--bd-primary), var(--bd-primary-dark));
        color: #fff;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        margin-right: 1rem;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: var(--bd-transition);
    }
    .form-control[type="file"]::file-selector-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 14px -4px rgba(16, 185, 129, 0.5);
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

    /* ===== Sentiment preview ===== */
    .sentiment-preview {
        background:
            radial-gradient(circle at 100% 0%, rgba(6, 182, 212, 0.1), transparent 50%),
            linear-gradient(135deg, #f0f9ff, #e0f2fe);
        border: 1px solid rgba(6, 182, 212, 0.25);
        border-radius: 14px;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        transition: var(--bd-transition);
        position: relative;
        overflow: hidden;
    }
    .sentiment-preview .ai-icon {
        width: 38px;
        height: 38px;
        border-radius: 11px;
        background: linear-gradient(135deg, var(--bd-accent), #0891b2);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
        box-shadow: 0 6px 14px -4px rgba(6, 182, 212, 0.5);
    }
    .sentiment-preview .ai-text {
        flex: 1;
        font-size: 0.9rem;
        color: var(--bd-gray);
        font-weight: 500;
    }
    .sentiment-preview .sentiment-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.4rem 0.9rem;
        border-radius: 100px;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.01em;
        border: 1px solid transparent;
        animation: bdZoomIn 0.3s ease;
    }
    .sentiment-badge.positive {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
        border-color: rgba(16, 185, 129, 0.3);
    }
    .sentiment-badge.negative {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
        border-color: rgba(239, 68, 68, 0.3);
    }
    .sentiment-badge.neutral {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
        border-color: rgba(245, 158, 11, 0.3);
    }

    /* ===== Pickup toggle ===== */
    .pickup-toggle {
        background: #f8fafc;
        border: 1px solid var(--bd-border);
        border-radius: 14px;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        transition: var(--bd-transition);
        cursor: pointer;
    }
    .pickup-toggle:hover {
        background: #f0fdf7;
        border-color: rgba(16, 185, 129, 0.25);
    }
    .pickup-toggle .form-check-input {
        width: 44px;
        height: 24px;
        margin: 0;
        cursor: pointer;
        border-radius: 100px;
        border: 2px solid var(--bd-border);
        background-color: #fff;
        background-image: none;
        transition: var(--bd-transition);
        position: relative;
        flex-shrink: 0;
    }
    .pickup-toggle .form-check-input:checked {
        background-color: var(--bd-primary);
        border-color: var(--bd-primary);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
    }
    .pickup-toggle .form-check-input:focus {
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
    }
    .pickup-toggle .form-check-label {
        font-weight: 600;
        color: var(--bd-dark);
        cursor: pointer;
        margin: 0;
        font-size: 0.92rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .pickup-toggle .form-check-label i {
        color: var(--bd-primary);
    }
    .pickup-toggle .hint {
        font-size: 0.78rem;
        color: var(--bd-gray);
        font-weight: 400;
        margin-left: auto;
    }

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

    /* ===== Validation feedback ===== */
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
    @keyframes bdZoomIn {
        from { opacity: 0; transform: scale(0.9); }
        to   { opacity: 1; transform: scale(1); }
    }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
        .donation-page { padding: 1.75rem 0 3.5rem; }
        .donation-header { padding: 1.5rem 1.25rem; }
        .donation-header .header-icon { width: 50px; height: 50px; font-size: 1.3rem; }
        .donation-body { padding: 1.5rem 1.25rem 1.75rem; }
        .form-actions { flex-direction: column-reverse; }
        .btn-modern { width: 100%; }
        .pickup-toggle { flex-wrap: wrap; }
        .pickup-toggle .hint { margin-left: 0; width: 100%; }
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
                                    <i class="fas fa-gift"></i> New Donation
                                </span>
                                <h1>
                                    Make a Donation
                                    <span class="sub">Give a second life to your items</span>
                                </h1>
                            </div>
                        </div>
                    </div>

                    {{-- ============================================================
                         BODY
                         ============================================================ --}}
                    <div class="donation-body">

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

                        <form action="{{ route($storeRoute) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            @csrf
                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                            {{-- ============ SECTION 1: Item Details ============ --}}
                            <h3 class="form-section-title">
                                <span class="section-icon"><i class="fas fa-box-open"></i></span>
                                Item Details
                            </h3>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="waste_category_id" class="form-label">
                                        <i class="fas fa-trash-alt"></i>Waste Category
                                    </label>
                                    <select name="waste_category_id" id="waste_category_id" class="form-select" required>
                                        <option value="">Select Waste Category</option>
                                        @if(isset($wasteCategories) && $wasteCategories->count() > 0)
                                            @foreach($wasteCategories as $wasteCategory)
                                                <option value="{{ $wasteCategory->id }}" {{ old('waste_category_id') == $wasteCategory->id ? 'selected' : '' }}>
                                                    {{ $wasteCategory->name }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="" disabled>No waste categories available</option>
                                        @endif
                                    </select>
                                    @error('waste_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="condition" class="form-label">
                                        <i class="fas fa-info-circle"></i>Condition
                                    </label>
                                    <select name="condition" id="condition" class="form-select" required>
                                        <option value="">Select Condition</option>
                                        <option value="new" {{ old('condition') == 'new' ? 'selected' : '' }}>New</option>
                                        <option value="used" {{ old('condition') == 'used' ? 'selected' : '' }}>Used</option>
                                        <option value="damaged" {{ old('condition') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                                    </select>
                                    @error('condition')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="item_name" class="form-label">
                                        <i class="fas fa-tag"></i>Item Name
                                    </label>
                                    <input type="text" name="item_name" id="item_name" value="{{ old('item_name') }}"
                                           class="form-control" placeholder="e.g. Wooden chair, Plastic bottles…" required>
                                    @error('item_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- ============ SECTION 2: Description ============ --}}
                            <h3 class="form-section-title">
                                <span class="section-icon"><i class="fas fa-align-left"></i></span>
                                Description & AI Analysis
                            </h3>

                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    <i class="fas fa-pen"></i>Description
                                    <span class="optional">(optional)</span>
                                </label>
                                <textarea name="description" id="description" rows="4" class="form-control"
                                          placeholder="Describe your item: condition, size, color, any details…">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-brain"></i>AI Sentiment Preview
                                    <span class="optional">(auto)</span>
                                </label>
                                <div id="sentiment-preview" class="sentiment-preview">
                                    <div class="ai-icon"><i class="fas fa-robot"></i></div>
                                    <div class="ai-text">Enter a description to analyze its sentiment with AI…</div>
                                </div>
                            </div>

                            {{-- ============ SECTION 3: Photos ============ --}}
                            <h3 class="form-section-title">
                                <span class="section-icon"><i class="fas fa-camera"></i></span>
                                Photos
                            </h3>

                            <div class="mb-3">
                                <label for="images" class="form-label">
                                    <i class="fas fa-images"></i>Upload Images
                                    <span class="optional">(multiple allowed)</span>
                                </label>
                                <input type="file" name="images[]" id="images" multiple class="form-control" accept="image/*">
                                @error('images.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ============ SECTION 4: Pickup ============ --}}
                            <h3 class="form-section-title">
                                <span class="section-icon"><i class="fas fa-truck"></i></span>
                                Pickup Options
                            </h3>

                            <div class="mb-3">
                                <label class="pickup-toggle" for="pickup_required">
                                    <input type="checkbox" name="pickup_required" id="pickup_required"
                                           class="form-check-input" value="1"
                                           {{ old('pickup_required') ? 'checked' : '' }}>
                                    <span class="form-check-label">
                                        <i class="fas fa-truck"></i>
                                        Pickup Required
                                    </span>
                                    <span class="hint">Check if you need us to pick up the item</span>
                                </label>
                                @error('pickup_required')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3" id="pickupAddressWrapper" style="{{ old('pickup_required') ? '' : 'display: none;' }}">
                                <label for="pickup_address" class="form-label">
                                    <i class="fas fa-map-marker-alt"></i>Pickup Address
                                </label>
                                <input type="text" name="pickup_address" id="pickup_address"
                                       value="{{ old('pickup_address') }}" class="form-control"
                                       placeholder="Full address for pickup…">
                                @error('pickup_address')
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
                                    <i class="fas fa-paper-plane"></i>
                                    Submit Donation
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
       AI SENTIMENT ANALYSIS
       ============================================================ */
    const descInput = document.getElementById('description');
    const sentimentPreview = document.getElementById('sentiment-preview');

    if (descInput && sentimentPreview) {
        let debounceTimer;

        descInput.addEventListener('input', function () {
            const desc = this.value.trim();

            clearTimeout(debounceTimer);

            if (desc.length < 11) {
                sentimentPreview.innerHTML = `
                    <div class="ai-icon"><i class="fas fa-robot"></i></div>
                    <div class="ai-text">Enter a description to analyze its sentiment with AI…</div>`;
                return;
            }

            sentimentPreview.innerHTML = `
                <div class="ai-icon"><i class="fas fa-spinner fa-spin"></i></div>
                <div class="ai-text">Analyzing sentiment…</div>`;

            debounceTimer = setTimeout(() => {
                fetch('{{ route("front.donations.analyze-sentiment") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ description: desc })
                })
                .then(res => res.json())
                .then(data => {
                    const sentiment = (data.sentiment || 'neutral').toLowerCase();
                    const icons = { positive: 'fa-face-smile', negative: 'fa-face-frown', neutral: 'fa-face-meh' };
                    sentimentPreview.innerHTML = `
                        <div class="ai-icon"><i class="fas fa-robot"></i></div>
                        <div class="ai-text">Sentiment detected by AI</div>
                        <span class="sentiment-badge ${sentiment}">
                            <i class="fas ${icons[sentiment] || icons.neutral}"></i>
                            ${sentiment.charAt(0).toUpperCase() + sentiment.slice(1)}
                        </span>`;
                })
                .catch(() => {
                    sentimentPreview.innerHTML = `
                        <div class="ai-icon"><i class="fas fa-robot"></i></div>
                        <div class="ai-text">Unable to analyze sentiment right now.</div>`;
                });
            }, 500);
        });
    }

    /* ============================================================
       PICKUP ADDRESS TOGGLE
       ============================================================ */
    const pickupCheckbox = document.getElementById('pickup_required');
    const pickupWrapper = document.getElementById('pickupAddressWrapper');
    const pickupAddress = document.getElementById('pickup_address');

    if (pickupCheckbox && pickupWrapper) {
        pickupCheckbox.addEventListener('change', function () {
            if (this.checked) {
                pickupWrapper.style.display = '';
                pickupWrapper.style.animation = 'bdFadeInUp 0.3s ease-out';
                if (pickupAddress) pickupAddress.setAttribute('required', 'required');
            } else {
                pickupWrapper.style.display = 'none';
                if (pickupAddress) {
                    pickupAddress.removeAttribute('required');
                    pickupAddress.value = '';
                }
            }
        });

        if (pickupCheckbox.checked && pickupAddress) {
            pickupAddress.setAttribute('required', 'required');
        }
    }

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