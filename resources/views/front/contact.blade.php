@extends('front.layout')

@section('title', 'Contact')

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

    /* ===== Hero ===== */
    .contact-hero {
        background:
            linear-gradient(135deg, rgba(6, 38, 25, 0.92), rgba(16, 185, 129, 0.55)),
            url('{{ asset('images/EarthFront.png') }}') center/cover no-repeat;
        min-height: 420px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #fff;
        padding: 140px 20px 80px;
        position: relative;
        overflow: hidden;
    }
    .contact-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 20% 30%, rgba(16, 185, 129, 0.25), transparent 45%),
            radial-gradient(circle at 80% 70%, rgba(6, 182, 212, 0.2), transparent 45%);
        pointer-events: none;
    }
    .contact-hero::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 80px;
        background: linear-gradient(to top, #f8fafc, transparent);
        pointer-events: none;
    }

    .contact-hero .hero-content {
        position: relative;
        z-index: 1;
        animation: bdFadeInUp 0.7s ease-out;
    }

    .contact-hero .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 0.5rem 1.1rem;
        border-radius: 100px;
        font-size: 0.82rem;
        font-weight: 600;
        margin-bottom: 1.25rem;
        letter-spacing: 0.02em;
    }
    .contact-hero .hero-badge i {
        color: #6ee7b7;
    }

    .contact-hero h1 {
        font-size: clamp(2.25rem, 5vw, 3.5rem);
        font-weight: 800;
        margin-bottom: 18px;
        letter-spacing: -0.02em;
        line-height: 1.1;
    }
    .contact-hero h1 .gradient-text {
        background: linear-gradient(135deg, #6ee7b7 0%, #67e8f9 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .contact-hero p {
        font-size: 1.08rem;
        max-width: 680px;
        margin: 0 auto;
        color: rgba(255, 255, 255, 0.88);
        line-height: 1.65;
    }

    /* ===== Contact info cards ===== */
    .contact-info-section {
        padding: 4rem 0 3rem;
    }

    .contact-card {
        border: 1px solid var(--bd-border);
        border-radius: var(--bd-radius);
        background: #fff;
        box-shadow: var(--bd-shadow-sm);
        padding: 2rem 1.5rem;
        height: 100%;
        text-align: center;
        transition: var(--bd-transition);
        position: relative;
        overflow: hidden;
        animation: bdFadeInUp 0.6s ease-out both;
    }
    .contact-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--bd-primary), var(--bd-accent));
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s ease;
    }
    .contact-card:hover::before {
        transform: scaleX(1);
    }
    .contact-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--bd-shadow-lg);
        border-color: rgba(16, 185, 129, 0.3);
    }

    .contact-card .icon-wrap {
        width: 68px;
        height: 68px;
        margin: 0 auto 1.1rem;
        border-radius: 18px;
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        color: var(--bd-primary-dark);
        transition: var(--bd-transition);
        box-shadow: 0 8px 20px -8px rgba(16, 185, 129, 0.4);
    }
    .contact-card:hover .icon-wrap {
        transform: scale(1.1) rotate(-5deg);
        background: linear-gradient(135deg, var(--bd-primary), var(--bd-primary-dark));
        color: #fff;
        box-shadow: 0 12px 26px -8px rgba(16, 185, 129, 0.6);
    }

    .contact-card h5 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--bd-dark);
        margin-bottom: 0.5rem;
        letter-spacing: -0.01em;
    }
    .contact-card p {
        color: var(--bd-gray);
        font-size: 0.9rem;
        margin: 0;
        font-weight: 500;
    }

    /* ===== Form / Info panel ===== */
    .form-panel {
        background: #fff;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: var(--bd-shadow-md);
        border: 1px solid var(--bd-border);
        height: 100%;
        position: relative;
        overflow: hidden;
        animation: bdFadeInUp 0.6s ease-out 0.15s both;
    }
    .form-panel::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--bd-primary), var(--bd-accent), var(--bd-primary));
        background-size: 200% 100%;
        animation: bdShimmer 3s linear infinite;
    }

    .form-panel h3 {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--bd-dark);
        margin-bottom: 0.75rem;
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 0.65rem;
    }
    .form-panel h3 i {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--bd-primary), var(--bd-primary-dark));
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        box-shadow: 0 8px 18px -6px rgba(16, 185, 129, 0.5);
    }

    .form-panel > p.text-muted {
        color: var(--bd-gray) !important;
        font-size: 0.98rem;
        line-height: 1.65;
        margin-bottom: 1.75rem;
    }

    /* ===== Feature list ===== */
    .feature-list {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }
    .feature-item {
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
        padding: 0.9rem 1rem;
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid transparent;
        transition: var(--bd-transition);
        font-size: 0.92rem;
        color: #334155;
        line-height: 1.5;
    }
    .feature-item:hover {
        background: #f0fdf7;
        border-color: rgba(16, 185, 129, 0.2);
        transform: translateX(6px);
    }
    .feature-item .feature-icon {
        flex-shrink: 0;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--bd-primary), var(--bd-primary-dark));
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        margin-top: 1px;
        box-shadow: 0 4px 10px -2px rgba(16, 185, 129, 0.4);
    }

    /* ===== Quick answer CTA ===== */
    .quick-answer {
        margin-top: 2rem;
        padding: 1.5rem;
        border-radius: 18px;
        background:
            radial-gradient(circle at 100% 0%, rgba(6, 182, 212, 0.15), transparent 50%),
            linear-gradient(135deg, #ecfdf5, #f0fdfa);
        border: 1px solid rgba(16, 185, 129, 0.25);
        position: relative;
        overflow: hidden;
    }
    .quick-answer::before {
        content: "";
        position: absolute;
        top: -40%;
        right: -5%;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.15), transparent 70%);
        pointer-events: none;
    }
    .quick-answer .qa-head {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 0.6rem;
        position: relative;
    }
    .quick-answer .qa-head .qa-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--bd-primary), var(--bd-primary-dark));
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        box-shadow: 0 6px 14px -4px rgba(16, 185, 129, 0.5);
    }
    .quick-answer .qa-head p {
        font-weight: 700;
        color: var(--bd-primary-darker);
        margin: 0;
        font-size: 1rem;
    }
    .quick-answer .qa-body {
        color: #475569;
        font-size: 0.92rem;
        line-height: 1.6;
        position: relative;
    }
    .quick-answer .qa-body strong {
        color: var(--bd-primary-darker);
        font-weight: 700;
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

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
        .contact-hero {
            min-height: 340px;
            padding: 110px 20px 60px;
        }
        .contact-hero p { font-size: 1rem; }
        .contact-info-section { padding: 2.5rem 0 2rem; }
        .form-panel { padding: 1.75rem; }
        .form-panel h3 { font-size: 1.35rem; }
        .contact-card { padding: 1.5rem 1.25rem; }
        .contact-card .icon-wrap { width: 58px; height: 58px; font-size: 1.4rem; }
    }
</style>

{{-- ============================================================
     HERO
     ============================================================ --}}
<section class="contact-hero">
    <div class="container">
        <div class="hero-content">
            <span class="hero-badge">
                <i class="bi bi-chat-dots-fill"></i> Get in touch with Biodex
            </span>
            <h1>Contact <span class="gradient-text">us</span></h1>
            <p>
                We are here to help you with questions, partnerships, donations, volunteering,
                or any sustainable initiative you want to start.
            </p>
        </div>
    </div>
</section>

{{-- ============================================================
     CONTACT INFO CARDS
     ============================================================ --}}
<section class="contact-info-section">
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-md-6 col-lg-3">
                <div class="contact-card">
                    <div class="icon-wrap">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <h5>Visit us</h5>
                    <p>Sidi Bouzid, Tunisia</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="contact-card">
                    <div class="icon-wrap">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                    <h5>Email</h5>
                    <p>contact@biodex.tn</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="contact-card">
                    <div class="icon-wrap">
                        <i class="bi bi-telephone-fill"></i>
                    </div>
                    <h5>Phone</h5>
                    <p>+216 12 345 678</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="contact-card">
                    <div class="icon-wrap">
                        <i class="bi bi-clock-fill"></i>
                    </div>
                    <h5>Hours</h5>
                    <p>Mon – Sat / 9:00 – 18:00</p>
                </div>
            </div>
        </div>

        {{-- ============================================================
             WHY CONTACT BIODEX
             ============================================================ --}}
        <div class="row g-4 align-items-stretch justify-content-center">
            <div class="col-lg-10">
                <div class="form-panel">
                    <h3>
                        <i class="bi bi-stars"></i>
                        Why contact Biodex?
                    </h3>
                    <p class="text-muted mb-4">
                        Whether you want to join a campaign, collaborate with our community,
                        or explore eco-partnership opportunities, we'd love to hear from you.
                    </p>

                    <div class="feature-list">
                        <div class="feature-item">
                            <span class="feature-icon"><i class="bi bi-check-lg"></i></span>
                            <span>Support for citizens and environmental initiatives</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon"><i class="bi bi-check-lg"></i></span>
                            <span>Partnerships with schools, NGOs, and businesses</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon"><i class="bi bi-check-lg"></i></span>
                            <span>Guidance for donations, volunteering, and collection programs</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon"><i class="bi bi-check-lg"></i></span>
                            <span>Answers about sustainable products and local recycling actions</span>
                        </div>
                    </div>

                    <div class="quick-answer">
                        <div class="qa-head">
                            <span class="qa-icon"><i class="bi bi-lightning-charge-fill"></i></span>
                            <p>Need a quick answer?</p>
                        </div>
                        <div class="qa-body">
                            Reach us directly at <strong>contact@biodex.tn</strong>
                            or call <strong>+216 12 345 678</strong>.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection