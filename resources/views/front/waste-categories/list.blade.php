@extends('front.layout')

@section('content')

{{-- ============================================================
     STYLES — DESIGN AMÉLIORÉ
     ============================================================ --}}
<style>
    :root {
        --wc-primary: #10b981;
        --wc-primary-dark: #059669;
        --wc-primary-darker: #047857;
        --wc-accent: #06b6d4;
        --wc-dark: #0f172a;
        --wc-gray: #64748b;
        --wc-light: #f8fafc;
        --wc-border: #e2e8f0;
        --wc-radius: 18px;
        --wc-shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.06);
        --wc-shadow-md: 0 10px 30px -8px rgba(15, 23, 42, 0.12);
        --wc-shadow-lg: 0 24px 50px -12px rgba(16, 185, 129, 0.25);
        --wc-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ===== Page shell ===== */
    .wc-page {
        background:
            radial-gradient(circle at 10% 0%, rgba(16, 185, 129, 0.08), transparent 45%),
            radial-gradient(circle at 90% 20%, rgba(6, 182, 212, 0.07), transparent 45%),
            linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        min-height: 100vh;
        padding: 3rem 0 5rem;
        position: relative;
    }

    /* ===== Hero title ===== */
    .wc-hero {
        text-align: center;
        margin-bottom: 3rem;
        animation: wcFadeInDown 0.6s ease-out;
    }
    .wc-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(16, 185, 129, 0.12);
        color: var(--wc-primary-dark);
        padding: 0.45rem 1rem;
        border-radius: 100px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 1rem;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }
    .wc-title {
        font-weight: 800;
        color: var(--wc-dark);
        font-size: clamp(1.8rem, 4vw, 2.75rem);
        margin-bottom: 0.75rem;
        letter-spacing: -0.02em;
        line-height: 1.15;
    }
    .wc-title .gradient-text {
        background: linear-gradient(135deg, var(--wc-primary) 0%, var(--wc-accent) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .wc-subtitle {
        color: var(--wc-gray);
        font-size: 1.05rem;
        max-width: 620px;
        margin: 0 auto;
        line-height: 1.6;
    }

    /* ===== AI Module ===== */
    .ai-classifier-panel {
        background: #fff;
        border-radius: 24px;
        box-shadow: var(--wc-shadow-md);
        overflow: hidden;
        border: 1px solid var(--wc-border);
        margin-bottom: 3rem;
        animation: wcFadeInUp 0.6s ease-out 0.1s both;
        position: relative;
    }
    .ai-classifier-panel::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--wc-primary), var(--wc-accent), var(--wc-primary));
        background-size: 200% 100%;
        animation: wcShimmer 3s linear infinite;
    }
    .ai-panel-head {
        background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%);
        color: #fff;
        padding: 1.75rem 2rem;
        position: relative;
        overflow: hidden;
    }
    .ai-panel-head::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image:
            radial-gradient(circle at 20% 30%, rgba(255,255,255,0.08) 0%, transparent 40%),
            radial-gradient(circle at 80% 70%, rgba(6, 182, 212, 0.15) 0%, transparent 40%);
        pointer-events: none;
    }
    .ai-panel-head::after {
        content: "";
        position: absolute;
        top: -50%;
        right: -5%;
        width: 340px;
        height: 340px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.35), transparent 65%);
        pointer-events: none;
        animation: wcPulse 4s ease-in-out infinite;
    }
    .ai-panel-head .head-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .ai-icon-wrap {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .ai-panel-head h2 {
        font-size: 1.3rem;
        margin: 0 0 0.25rem;
        font-weight: 700;
        letter-spacing: -0.01em;
    }
    .ai-panel-head p {
        margin: 0;
        font-size: 0.88rem;
        opacity: 0.85;
        max-width: 640px;
        line-height: 1.5;
    }
    .ai-panel-body { padding: 2rem; }

    /* ===== Dropzone ===== */
    .ai-dropzone {
        border: 2px dashed #a7d9c0;
        border-radius: var(--wc-radius);
        padding: 3rem 1.5rem;
        text-align: center;
        cursor: pointer;
        background:
            radial-gradient(circle at 50% 0%, rgba(16, 185, 129, 0.06), transparent 70%),
            linear-gradient(180deg, #f8fdfa, #f0fdf7);
        transition: var(--wc-transition);
        position: relative;
        overflow: hidden;
    }
    .ai-dropzone::before {
        content: "";
        position: absolute;
        inset: 0;
        background: repeating-linear-gradient(
            45deg,
            transparent,
            transparent 10px,
            rgba(16, 185, 129, 0.02) 10px,
            rgba(16, 185, 129, 0.02) 20px
        );
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }
    .ai-dropzone:hover::before,
    .ai-dropzone.dragover::before { opacity: 1; }
    .ai-dropzone:hover,
    .ai-dropzone.dragover {
        border-color: var(--wc-primary);
        background: #ecfdf5;
        transform: translateY(-3px);
        box-shadow: var(--wc-shadow-lg);
    }
    .ai-dropzone .icon-circle {
        width: 72px;
        height: 72px;
        margin: 0 auto 1rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.9rem;
        color: var(--wc-primary-dark);
        transition: var(--wc-transition);
        box-shadow: 0 8px 20px -6px rgba(16, 185, 129, 0.4);
    }
    .ai-dropzone:hover .icon-circle,
    .ai-dropzone.dragover .icon-circle {
        transform: scale(1.1) rotate(-5deg);
        background: linear-gradient(135deg, var(--wc-primary), var(--wc-primary-dark));
        color: #fff;
    }
    .ai-dropzone p { color: var(--wc-dark); font-size: 1rem; margin: 0 0 0.35rem; font-weight: 600; }
    .ai-dropzone small { color: var(--wc-gray); font-size: 0.83rem; }

    #imagePreview {
        display: none;
        max-height: 300px;
        border-radius: var(--wc-radius);
        margin: 1.25rem auto 0;
        box-shadow: var(--wc-shadow-md);
        animation: wcZoomIn 0.4s ease;
    }

    .result-hidden { display: none; }

    /* ===== Result cards ===== */
    .ai-result-card {
        border-radius: var(--wc-radius);
        border: 1px solid var(--wc-border);
        background: linear-gradient(180deg, #f8fbfa, #f1f9f5);
        padding: 1.5rem;
        height: 100%;
        position: relative;
        overflow: hidden;
        transition: var(--wc-transition);
    }
    .ai-result-card:hover {
        box-shadow: var(--wc-shadow-md);
        transform: translateY(-2px);
    }
    .ai-result-card .label {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--wc-gray);
        font-weight: 700;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .ai-result-card .label::before {
        content: "";
        width: 3px;
        height: 12px;
        background: var(--wc-primary);
        border-radius: 2px;
    }
    .ai-result-category {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--wc-dark);
        line-height: 1.15;
        letter-spacing: -0.02em;
    }
    .ai-result-category .sub {
        display: block;
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--wc-gray);
        margin-top: 0.35rem;
        letter-spacing: 0;
    }

    .ai-confidence-bar {
        height: 10px;
        background: #e2e8f0;
        border-radius: 100px;
        overflow: hidden;
        margin: 0.6rem 0;
        position: relative;
    }
    .ai-confidence-bar > div {
        height: 100%;
        border-radius: 100px;
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        background-size: 200% 100%;
        animation: wcShimmer 2s linear infinite;
    }

    .ai-top-list .list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0.5rem;
        border-bottom: 1px solid #eef2ef;
        font-size: 0.92rem;
        transition: var(--wc-transition);
    }
    .ai-top-list .list-item:last-child { border-bottom: 0; }
    .ai-top-list .list-item:hover {
        background: rgba(16, 185, 129, 0.05);
        padding-left: 0.75rem;
        border-radius: 8px;
    }
    .ai-top-list .list-item strong {
        color: var(--wc-primary-dark);
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }
    .ai-top-list .rank-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        border-radius: 6px;
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: var(--wc-primary-dark);
        font-size: 0.7rem;
        font-weight: 700;
        margin-right: 8px;
    }

    .badge-ai {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: var(--wc-primary-darker);
        font-weight: 600;
        padding: 0.45rem 0.9rem;
        border-radius: 100px;
        font-size: 0.78rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid rgba(16, 185, 129, 0.25);
    }

    .ai-spinner-wrap {
        display: none;
        text-align: center;
        padding: 2.5rem;
    }

    /* ===== Toolbar ===== */
    .wc-toolbar {
        background: #fff;
        border-radius: var(--wc-radius);
        padding: 1.25rem 1.5rem;
        box-shadow: var(--wc-shadow-sm);
        border: 1px solid var(--wc-border);
        margin-bottom: 1.5rem;
        animation: wcFadeInUp 0.6s ease-out 0.2s both;
    }

    .wc-toolbar .input-group {
        border-radius: 12px;
        overflow: hidden;
        transition: var(--wc-transition);
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
    }
    .wc-toolbar .input-group:focus-within {
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
    }
    .wc-toolbar .input-group-text {
        background: linear-gradient(135deg, var(--wc-primary), var(--wc-primary-dark));
        color: #fff;
        border: none;
        padding: 0 1rem;
    }
    .wc-toolbar .form-control {
        border: 1px solid var(--wc-border);
        border-radius: 0;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        background: #fff;
    }
    .wc-toolbar .form-control:focus {
        box-shadow: none;
        border-color: var(--wc-primary);
    }
    .wc-toolbar .btn-outline-secondary {
        border: 1px solid var(--wc-border);
        border-left: none;
        border-radius: 0 12px 12px 0;
        color: var(--wc-gray);
        transition: var(--wc-transition);
    }
    .wc-toolbar .btn-outline-secondary:hover {
        background: #fee2e2;
        color: #dc2626;
        border-color: #fecaca;
    }
    .wc-toolbar .form-select {
        border: 1px solid var(--wc-border);
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 0.92rem;
        transition: var(--wc-transition);
        cursor: pointer;
    }
    .wc-toolbar .form-select:focus {
        border-color: var(--wc-primary);
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.12);
    }

    /* ===== Results bar ===== */
    .wc-results-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 1.5rem;
    }
    .results-count {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        padding: 0.55rem 1.1rem;
        border-radius: 100px;
        color: var(--wc-primary-darker);
        font-weight: 600;
        font-size: 0.88rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }
    .results-count .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--wc-primary);
        animation: wcPulseDot 1.5s ease-in-out infinite;
    }

    .filter-tags { display: flex; gap: 8px; flex-wrap: wrap; }
    .filter-tag {
        background: linear-gradient(135deg, #dbeafe, #e0f2fe);
        color: #0369a1;
        padding: 0.4rem 0.85rem;
        border-radius: 100px;
        font-size: 0.78rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
        border: 1px solid rgba(3, 105, 161, 0.15);
        animation: wcZoomIn 0.25s ease;
    }
    .filter-tag .close {
        cursor: pointer;
        margin-left: 4px;
        font-weight: 700;
        opacity: 0.6;
        transition: var(--wc-transition);
        width: 16px;
        height: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    .filter-tag .close:hover {
        opacity: 1;
        background: rgba(3, 105, 161, 0.15);
    }

    /* ===== Category cards ===== */
    .collection-card {
        border: 1px solid var(--wc-border);
        border-radius: var(--wc-radius);
        transition: var(--wc-transition);
        height: 100%;
        overflow: hidden;
        background: #fff;
        position: relative;
        animation: wcFadeInUp 0.5s ease-out both;
    }
    .collection-card::before {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: var(--wc-radius);
        padding: 2px;
        background: linear-gradient(135deg, var(--wc-primary), var(--wc-accent));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }
    .collection-card:hover::before { opacity: 1; }
    .collection-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px -12px rgba(16, 185, 129, 0.25);
        border-color: transparent;
    }

    .card-header {
        background: linear-gradient(135deg, #064e3b 0%, var(--wc-primary-darker) 100%);
        color: #fff;
        border-radius: 0 !important;
        padding: 1.25rem 1.5rem;
        border: none;
        position: relative;
        overflow: hidden;
    }
    .card-header::after {
        content: "";
        position: absolute;
        top: -50%;
        right: -20%;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.4), transparent 70%);
        transition: var(--wc-transition);
    }
    .collection-card:hover .card-header::after {
        transform: scale(1.3);
    }
    .card-header h5 {
        font-size: 1.05rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        position: relative;
        z-index: 1;
        letter-spacing: -0.01em;
    }
    .card-header h5 i {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.15);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        margin-right: 0.65rem;
        backdrop-filter: blur(10px);
    }
    .card-body { padding: 1.5rem; }
    .card-text {
        color: #475569;
        font-size: 0.92rem;
        line-height: 1.65;
        min-height: 60px;
        margin-bottom: 1rem;
    }
    .card-body small {
        font-size: 0.83rem;
        line-height: 1.5;
        display: block;
        padding: 0.75rem;
        background: #f8fafc;
        border-radius: 10px;
        border-left: 3px solid var(--wc-primary);
    }

    .collection-card .btn-success {
        background: linear-gradient(135deg, var(--wc-primary), var(--wc-primary-dark));
        border: none;
        border-radius: 12px;
        padding: 0.7rem 1.25rem;
        font-weight: 600;
        font-size: 0.92rem;
        transition: var(--wc-transition);
        position: relative;
        overflow: hidden;
    }
    .collection-card .btn-success::before {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent);
        transition: left 0.5s ease;
    }
    .collection-card .btn-success:hover::before { left: 100%; }
    .collection-card .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -6px rgba(16, 185, 129, 0.5);
    }

    .category-card { transition: all 0.3s ease; }
    .category-card.hidden { display: none; }

    .highlight {
        background: linear-gradient(180deg, transparent 55%, #fef08a 55%);
        padding: 0 2px;
        border-radius: 3px;
        font-weight: 600;
        color: var(--wc-dark);
    }

    /* ===== Empty state ===== */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: var(--wc-gray);
        animation: wcFadeInUp 0.5s ease;
    }
    .empty-state-icon {
        width: 100px;
        height: 100px;
        margin: 0 auto 24px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: var(--wc-primary);
        box-shadow: 0 12px 30px -10px rgba(16, 185, 129, 0.35);
    }
    .empty-state h3 {
        color: var(--wc-dark);
        font-weight: 700;
        font-size: 1.35rem;
    }

    /* ===== Modals ===== */
    .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.3);
        overflow: hidden;
    }
    .modal-header {
        border: none;
        padding: 1.5rem 1.75rem;
        background: linear-gradient(135deg, #064e3b 0%, var(--wc-primary-darker) 100%);
        position: relative;
        overflow: hidden;
    }
    .modal-header::after {
        content: "";
        position: absolute;
        top: -60%;
        right: -10%;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.35), transparent 65%);
    }
    .modal-title {
        font-weight: 700;
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 0.65rem;
    }
    .modal-body { padding: 1.75rem; }
    .modal-body h6 {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--wc-primary-dark);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }
    .modal-body p { color: #475569; line-height: 1.7; }
    .modal-footer { border: none; padding: 1rem 1.75rem 1.5rem; }

    /* ============================================================
       PAGINATION
       ============================================================ */
    .wc-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
        margin-top: 3rem;
        flex-wrap: wrap;
    }
    .wc-pagination .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 44px;
        height: 44px;
        padding: 0 0.9rem;
        border-radius: 12px;
        border: 1px solid var(--wc-border);
        background: #fff;
        color: var(--wc-primary-dark);
        font-weight: 600;
        font-size: 0.92rem;
        cursor: pointer;
        transition: var(--wc-transition);
        text-decoration: none;
    }
    .wc-pagination .page-btn:hover:not(:disabled):not(.active) {
        background: #ecfdf5;
        border-color: var(--wc-primary);
        transform: translateY(-3px);
        box-shadow: 0 8px 18px -6px rgba(16, 185, 129, 0.35);
    }
    .wc-pagination .page-btn.active {
        background: linear-gradient(135deg, var(--wc-primary), var(--wc-primary-dark));
        border-color: var(--wc-primary);
        color: #fff;
        box-shadow: 0 10px 22px -8px rgba(16, 185, 129, 0.55);
        transform: translateY(-2px);
    }
    .wc-pagination .page-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        background: #f8fafc;
    }
    .wc-pagination .page-btn i { font-size: 0.8rem; }
    .wc-pagination .page-info {
        font-size: 0.88rem;
        color: var(--wc-gray);
        padding: 0 0.75rem;
        font-weight: 500;
    }

    /* ===== Animations ===== */
    @keyframes wcFadeInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes wcFadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes wcZoomIn {
        from { opacity: 0; transform: scale(0.9); }
        to   { opacity: 1; transform: scale(1); }
    }
    @keyframes wcPulse {
        0%, 100% { transform: scale(1); opacity: 0.8; }
        50%      { transform: scale(1.15); opacity: 1; }
    }
    @keyframes wcPulseDot {
        0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.5); }
        50%      { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
    }
    @keyframes wcShimmer {
        0%   { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
        .wc-page { padding: 1.75rem 0 3.5rem; }
        .ai-panel-body { padding: 1.25rem; }
        .ai-panel-head { padding: 1.25rem 1.25rem; }
        .ai-panel-head .head-content { flex-direction: column; text-align: center; }
        .wc-toolbar { padding: 1rem; }
        .ai-dropzone { padding: 2rem 1rem; }
        .ai-dropzone .icon-circle { width: 60px; height: 60px; font-size: 1.5rem; }
        .wc-pagination .page-btn {
            min-width: 40px;
            height: 40px;
            font-size: 0.85rem;
        }
    }
</style>

<div class="wc-page">
    <div class="container">

        {{-- ============ HERO TITLE ============ --}}
        <div class="wc-hero">
        
            <h1 class="wc-title">
                <i class="fas fa-recycle me-2" style="color: var(--wc-primary);"></i>
                Waste <span class="gradient-text">Categories</span>
            </h1>
            <p class="wc-subtitle">
                Explore, search and classify waste categories — powered by artificial intelligence
            </p>
        </div>

        {{-- ============================================================
             AI MODULE — AUTOMATIC CLASSIFICATION
             ============================================================ --}}
        <div class="ai-classifier-panel">
            <div class="ai-panel-head">
                <div class="head-content">
                    <div class="ai-icon-wrap">
                        <i class="bi bi-cpu"></i>
                    </div>
                    <div>
                        <h2>Automatic AI Classification</h2>
                        <p>Upload a photo of waste — the category is detected by a neural network (MobileNetV2 / ImageNet).</p>
                    </div>
                </div>
            </div>

            <div class="ai-panel-body">
                <input type="file" id="imageInput" name="image" accept="image/*" class="d-none">
                <div class="ai-dropzone" id="dropzone">
                    <div class="icon-circle">
                        <i class="bi bi-cloud-arrow-up"></i>
                    </div>
                    <p>Click or drop a waste photo here</p>
                    <small>JPG, PNG, GIF or WEBP — max 6 MB</small>
                </div>

                <div class="text-center">
                    <img id="imagePreview" alt="Image preview">
                </div>

                <div class="d-grid mt-4">
                    <button id="analyzeBtn" class="btn btn-success btn-lg" disabled
                            style="border-radius: 14px; padding: 0.85rem; font-weight: 600; background: linear-gradient(135deg, #10b981, #059669); border: none;">
                        <i class="bi bi-stars me-2"></i>Analyze with AI
                    </button>
                </div>

                <div id="loading" class="ai-spinner-wrap">
                    <div class="spinner-border text-success" role="status"></div>
                    <p class="mt-3 text-muted mb-0 fw-semibold">Analyzing your image…</p>
                </div>

                {{-- Result --}}
                <div id="result" class="result-hidden row g-3 mt-4">
                    <div class="col-md-6">
                        <div class="ai-result-card">
                            <div class="label">Detected category</div>
                            <div class="ai-result-category" id="resCategory">
                                —<span class="sub">Waiting for analysis</span>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted fw-semibold">Confidence</span>
                                    <span id="resConfidencePct" class="fw-bold" style="color: var(--wc-primary-dark);">0%</span>
                                </div>
                                <div class="ai-confidence-bar">
                                    <div id="resConfidenceBar" style="width:0%;background:linear-gradient(90deg, #10b981, #06b6d4)"></div>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-3 small flex-wrap">
                                <span class="badge-ai" id="resModel">
                                    <i class="bi bi-cpu"></i> —
                                </span>
                                <span class="badge-ai" id="resSource" style="background: #e0f2fe; color: #0369a1; border-color: rgba(3, 105, 161, 0.2);">
                                    <i class="bi bi-diagram-3"></i> —
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ai-result-card" style="background:#fff;">
                            <div class="label">Top detected categories</div>
                            <div class="ai-top-list" id="resTop"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================
             FILTERS — Search + Sort
             ============================================================ --}}
        <div class="wc-toolbar">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-8">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control"
                               placeholder="Search by name, description or instructions…">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <select id="sortSelect" class="form-select">
                        <option value="name_asc">Sort: Name A → Z</option>
                        <option value="name_desc">Sort: Name Z → A</option>
                        <option value="newest">Sort: Newest first</option>
                        <option value="oldest">Sort: Oldest first</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Results bar --}}
        <div class="wc-results-bar">
            <div class="results-count">
                <span class="dot"></span>
                <span id="resultsCount">{{ $categories->count() }}</span> categor(y/ies) found
            </div>
            <div class="filter-tags" id="filterTags"></div>
        </div>

        @if($categories->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-folder-open"></i></div>
                <h3 class="mb-2">No categories available</h3>
                <p class="text-muted mb-0">There are no waste categories yet.</p>
            </div>
        @else
            {{-- ============ CATEGORY GRID ============ --}}
            <div class="row g-4" id="categoriesContainer">
                @foreach($categories as $category)
                    <div class="col-lg-4 col-md-6 category-card"
                         data-name="{{ strtolower($category->name) }}"
                         data-description="{{ strtolower($category->description) }}"
                         data-instructions="{{ strtolower($category->recycling_instructions) }}"
                         data-created="{{ $category->created_at->timestamp }}">
                        <div class="collection-card card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-recycle"></i>{{ $category->name }}
                                </h5>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <p class="card-text">{{ Str::limit($category->description, 100) }}</p>

                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-lightbulb me-1" style="color: var(--wc-primary);"></i>
                                        {{ Str::limit($category->recycling_instructions, 80) }}
                                    </small>
                                </div>

                                <div class="d-grid mt-auto">
                                    <button class="btn btn-success"
                                            data-bs-toggle="modal"
                                            data-bs-target="#categoryModal{{ $category->id }}">
                                        <i class="fas fa-eye me-2"></i>View details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal --}}
                    <div class="modal fade" id="categoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header text-white">
                                    <h5 class="modal-title">
                                        <i class="fas fa-recycle"></i>{{ $category->name }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h6><i class="fas fa-align-left"></i>Description</h6>
                                    <p class="mb-4">{{ $category->description }}</p>

                                    <h6><i class="fas fa-recycle"></i>Recycling instructions</h6>
                                    <p class="mb-4">{{ $category->recycling_instructions }}</p>

                                    @if($category->created_at)
                                        <div class="p-3 rounded" style="background: #f0fdf7; border-left: 3px solid var(--wc-primary);">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                Created on {{ $category->created_at->format('m/d/Y') }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                                            style="border-radius: 10px; padding: 0.6rem 1.25rem;">
                                        <i class="fas fa-times me-2"></i>Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ============ PAGINATION ============ --}}
            <div id="paginationContainer">
                <div class="wc-pagination" id="wcPagination"></div>
            </div>

            {{-- No Results --}}
            <div id="noResults" class="empty-state" style="display: none;">
                <div class="empty-state-icon" style="background: linear-gradient(135deg, #fee2e2, #fecaca); color: #dc2626; box-shadow: 0 12px 30px -10px rgba(220, 38, 38, 0.35);">
                    <i class="fas fa-search"></i>
                </div>
                <h3 class="mb-2">No categories found</h3>
                <p class="text-muted mb-4">Try adjusting your search or filters.</p>
                <button class="btn btn-outline-success" id="resetFilters"
                        style="border-radius: 12px; padding: 0.7rem 1.5rem; font-weight: 600;">
                    <i class="fas fa-redo me-2"></i>Reset filters
                </button>
            </div>
        @endif
    </div>
</div>

{{-- ============================================================
     SCRIPTS
     ============================================================ --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    /* ============================================================
       AI — IMAGE CLASSIFICATION
       ============================================================ */
    (function () {
        const input = document.getElementById('imageInput');
        const dropzone = document.getElementById('dropzone');
        const preview = document.getElementById('imagePreview');
        const analyzeBtn = document.getElementById('analyzeBtn');
        const loading = document.getElementById('loading');
        const result = document.getElementById('result');

        if (!input || !dropzone) return;

        let selectedFile = null;

        function setFile(file) {
            if (!file || !file.type.startsWith('image/')) return;
            selectedFile = file;
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
            analyzeBtn.disabled = false;
        }

        dropzone.addEventListener('click', () => input.click());
        dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('dragover'); });
        dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dragover'));
        dropzone.addEventListener('drop', e => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
            setFile(e.dataTransfer.files[0]);
        });
        input.addEventListener('change', () => setFile(input.files[0]));

        analyzeBtn.addEventListener('click', async () => {
            if (!selectedFile) return;
            result.classList.add('result-hidden');
            loading.style.display = 'block';
            analyzeBtn.disabled = true;

            const formData = new FormData();
            formData.append('image', selectedFile);
            formData.append('_token', '{{ csrf_token() }}');

            try {
                const response = await fetch('{{ route('waste.classify') }}', { method: 'POST', body: formData });
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Classification failed');
                }
                renderResult(data);
            } catch (error) {
                alert('Error: ' + error.message);
            } finally {
                loading.style.display = 'none';
                analyzeBtn.disabled = false;
            }
        });

        function renderResult(data) {
            document.getElementById('resCategory').innerHTML =
                `${data.category_label || data.category}<span class="sub">AI-detected waste category</span>`;

            const confidence = Math.round((data.confidence || 0) * 100);
            document.getElementById('resConfidencePct').textContent = confidence + '%';

            const bar = document.getElementById('resConfidenceBar');
            bar.style.width = Math.max(4, confidence) + '%';
            bar.style.background = confidence >= 60
                ? 'linear-gradient(90deg, #10b981, #06b6d4)'
                : confidence >= 35
                    ? 'linear-gradient(90deg, #f59e0b, #eab308)'
                    : 'linear-gradient(90deg, #ef4444, #dc2626)';

            document.getElementById('resModel').innerHTML =
                `<i class="bi bi-cpu"></i> ${data.model || 'n/a'}`;
            document.getElementById('resSource').innerHTML =
                data.model === 'mobilenetv2-imagenet' ? '<i class="bi bi-diagram-3"></i> CNN network'
                    : data.model === 'heuristic-pil' ? '<i class="bi bi-tools"></i> heuristic (fallback)'
                        : '<i class="bi bi-exclamation-triangle"></i> service unavailable';

            const top = document.getElementById('resTop');
            const list = Array.isArray(data.top_categories) && data.top_categories.length
                ? data.top_categories
                : [{ label: data.category_label || data.category, confidence: data.confidence || 0 }];

            top.replaceChildren(...list.map((item, idx) => {
                const row = document.createElement('div');
                row.className = 'list-item';
                row.innerHTML = `
                    <span>
                        <span class="rank-badge">${idx + 1}</span>
                        ${item.label}
                    </span>
                    <strong>${Math.round((item.confidence || 0) * 100)}%</strong>`;
                return row;
            }));

            result.classList.remove('result-hidden');
        }
    })();

    /* ============================================================
       SEARCH + SORT + FILTERS + PAGINATION
       ============================================================ */
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');
    const sortSelect = document.getElementById('sortSelect');
    const categoriesContainer = document.getElementById('categoriesContainer');
    const categoryCards = Array.from(document.querySelectorAll('.category-card'));
    const resultsCount = document.getElementById('resultsCount');
    const noResults = document.getElementById('noResults');
    const filterTags = document.getElementById('filterTags');
    const resetFilters = document.getElementById('resetFilters');
    const paginationEl = document.getElementById('wcPagination');

    const PER_PAGE = 6;
    let currentSearch = '';
    let currentSort = 'name_asc';
    let currentPage = 1;
    let filteredCards = [...categoryCards];

    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            currentSearch = e.target.value.toLowerCase().trim();
            currentPage = 1;
            updateFilterTags();
            applyFilters();
        });

        clearSearch.addEventListener('click', function() {
            searchInput.value = '';
            currentSearch = '';
            currentPage = 1;
            updateFilterTags();
            applyFilters();
        });

        sortSelect.addEventListener('change', function(e) {
            currentSort = e.target.value;
            currentPage = 1;
            updateFilterTags();
            applyFilters();
        });

        resetFilters?.addEventListener('click', function() {
            searchInput.value = '';
            sortSelect.value = 'name_asc';
            currentSearch = '';
            currentSort = 'name_asc';
            currentPage = 1;
            updateFilterTags();
            applyFilters();
        });

        applyFilters();
    }

    function applyFilters() {
        filteredCards = categoryCards.filter(card => {
            const name = card.dataset.name;
            const description = card.dataset.description;
            const instructions = card.dataset.instructions;

            return !currentSearch ||
                name.includes(currentSearch) ||
                description.includes(currentSearch) ||
                instructions.includes(currentSearch);
        });

        filteredCards.sort((a, b) => {
            switch (currentSort) {
                case 'name_asc':  return a.dataset.name.localeCompare(b.dataset.name);
                case 'name_desc': return b.dataset.name.localeCompare(a.dataset.name);
                case 'newest':    return parseInt(b.dataset.created) - parseInt(a.dataset.created);
                case 'oldest':    return parseInt(a.dataset.created) - parseInt(b.dataset.created);
                default:          return 0;
            }
        });

        if (resultsCount) resultsCount.textContent = filteredCards.length;

        if (filteredCards.length === 0) {
            if (noResults) noResults.style.display = 'block';
            if (categoriesContainer) categoriesContainer.style.display = 'none';
            if (paginationEl) paginationEl.innerHTML = '';
            return;
        }

        if (noResults) noResults.style.display = 'none';
        if (categoriesContainer) categoriesContainer.style.display = 'flex';

        const totalPages = Math.ceil(filteredCards.length / PER_PAGE);
        if (currentPage > totalPages) currentPage = totalPages;

        categoryCards.forEach(card => {
            card.style.display = 'none';
            card.classList.add('hidden');
        });

        const start = (currentPage - 1) * PER_PAGE;
        const end = start + PER_PAGE;
        const pageCards = filteredCards.slice(start, end);

        pageCards.forEach(card => {
            card.style.display = '';
            card.classList.remove('hidden');
            categoriesContainer.appendChild(card);
        });

        renderPagination(totalPages);

        if (currentSearch) highlightSearchTerms(currentSearch);
        else removeHighlights();
    }

    function renderPagination(totalPages) {
        if (!paginationEl) return;

        if (totalPages <= 1) {
            paginationEl.innerHTML = '';
            return;
        }

        const frag = document.createDocumentFragment();

        const prev = document.createElement('button');
        prev.className = 'page-btn';
        prev.disabled = currentPage === 1;
        prev.innerHTML = '<i class="fas fa-chevron-left"></i>';
        prev.addEventListener('click', () => goToPage(currentPage - 1));
        frag.appendChild(prev);

        const pages = buildPageRange(currentPage, totalPages);
        pages.forEach(p => {
            const btn = document.createElement('button');
            btn.className = 'page-btn' + (p === currentPage ? ' active' : '');
            if (p === '...') {
                btn.textContent = '…';
                btn.disabled = true;
                btn.style.cursor = 'default';
                btn.style.border = 'none';
                btn.style.background = 'transparent';
            } else {
                btn.textContent = p;
                btn.addEventListener('click', () => goToPage(p));
            }
            frag.appendChild(btn);
        });

        const next = document.createElement('button');
        next.className = 'page-btn';
        next.disabled = currentPage === totalPages;
        next.innerHTML = '<i class="fas fa-chevron-right"></i>';
        next.addEventListener('click', () => goToPage(currentPage + 1));
        frag.appendChild(next);

        const info = document.createElement('span');
        info.className = 'page-info';
        info.textContent = `Page ${currentPage} / ${totalPages}`;
        frag.appendChild(info);

        paginationEl.replaceChildren(frag);
    }

    function buildPageRange(current, total) {
        const delta = 1;
        const range = [];
        const left = Math.max(2, current - delta);
        const right = Math.min(total - 1, current + delta);

        range.push(1);
        if (left > 2) range.push('...');
        for (let i = left; i <= right; i++) range.push(i);
        if (right < total - 1) range.push('...');
        if (total > 1) range.push(total);

        return range;
    }

    function goToPage(page) {
        const totalPages = Math.ceil(filteredCards.length / PER_PAGE);
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        applyFilters();

        const top = document.querySelector('.wc-results-bar');
        if (top) top.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function highlightSearchTerms(term) {
        removeHighlights();
        const cards = document.querySelectorAll('.category-card:not(.hidden)');
        cards.forEach(card => {
            card.querySelectorAll('.card-title, .card-text, small').forEach(el => {
                if (el.dataset.original === undefined) {
                    el.dataset.original = el.innerHTML;
                }
                const regex = new RegExp(`(${term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                el.innerHTML = el.dataset.original.replace(regex, '<span class="highlight">$1</span>');
            });
        });
    }

    function removeHighlights() {
        document.querySelectorAll('.highlight').forEach(h => {
            const parent = h.parentNode;
            parent.replaceChild(document.createTextNode(h.textContent), h);
            parent.normalize();
        });
    }

    function updateFilterTags() {
        if (!filterTags) return;
        filterTags.innerHTML = '';

        if (currentSearch) {
            const searchTag = document.createElement('span');
            searchTag.className = 'filter-tag';
            searchTag.innerHTML = `<i class="fas fa-search"></i> "${currentSearch}" <span class="close" onclick="clearSearchTag('search')">×</span>`;
            filterTags.appendChild(searchTag);
        }

        if (currentSort && currentSort !== 'name_asc') {
            const sortText = sortSelect.options[sortSelect.selectedIndex].text;
            const sortTag = document.createElement('span');
            sortTag.className = 'filter-tag';
            sortTag.innerHTML = `<i class="fas fa-sort"></i> ${sortText} <span class="close" onclick="clearSearchTag('sort')">×</span>`;
            filterTags.appendChild(sortTag);
        }
    }

    window.clearSearchTag = function(type) {
        if (type === 'search') {
            searchInput.value = '';
            currentSearch = '';
        } else if (type === 'sort') {
            sortSelect.value = 'name_asc';
            currentSort = 'name_asc';
        }
        currentPage = 1;
        updateFilterTags();
        applyFilters();
    };
});
</script>
@endsection