@extends('front.layout')

@section('content')
<style>
    .about-hero {
        background: linear-gradient(135deg, rgba(11, 61, 37, 0.88), rgba(20, 92, 62, 0.72)),
                    url('{{ asset('images/EarthFront.png') }}') center/cover no-repeat;
        min-height: 420px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-align: center;
        padding: 120px 20px 60px;
    }

    .about-hero h1 {
        font-size: clamp(2.4rem, 4vw, 4rem);
        font-weight: 800;
        margin-bottom: 20px;
    }

    .about-hero p {
        font-size: 1.15rem;
        max-width: 760px;
        margin: 0 auto;
        color: rgba(255,255,255,0.9);
    }

    .section-title {
        font-size: 2rem;
        font-weight: 800;
        color: #1f6f46;
        margin-bottom: 18px;
    }

    .stat-card,
    .value-card,
    .step-card,
    .contact-card {
        border: none;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 10px 30px rgba(21, 87, 52, 0.08);
    }

    .stat-card {
        padding: 28px 20px;
        text-align: center;
    }

    .stat-card .number {
        display: block;
        font-size: 2.2rem;
        font-weight: 800;
        color: #198754;
    }

    .value-card {
        padding: 26px 22px;
        height: 100%;
    }

    .value-card i {
        font-size: 2rem;
        color: #198754;
        margin-bottom: 12px;
    }

    .step-card {
        padding: 24px 20px;
        height: 100%;
    }

    .step-number {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .cta-box {
        background: linear-gradient(135deg, #eafaf1, #dff7ea);
        border: 1px solid rgba(25, 135, 84, 0.15);
        border-radius: 22px;
        padding: 40px 26px;
    }
</style>

<section class="about-hero">
    <div class="container">
        <h1>We build a cleaner future together</h1>
        <p>
            Biodex helps citizens, communities, and businesses transform waste into value through
            collection, recycling, education, and practical eco-solutions.
        </p>
    </div>
</section>

<section class="py-5">
    <div class="container">
      

        <div class="row align-items-center g-4 mb-5">
            <div class="col-lg-6">
                <h2 class="section-title">Our mission</h2>
                <p class="text-muted fs-5 mb-3">
                    Biodex exists to turn environmental challenges into everyday opportunities by making
                    waste sorting, repair, recycling, and green consumption easier, more rewarding, and
                    more accessible.
                </p>
                <p class="text-muted">
                    We believe that sustainable change begins with simple actions, strong local communities,
                    and transparent systems that connect people to better habits and concrete impact.
                </p>
            </div>
            <div class="col-lg-6">
                <img src="{{ asset('images/EarthDayBanner.jpg') }}" alt="Biodex mission" class="img-fluid rounded-4 shadow-sm w-100" style="height: 360px; object-fit: cover;">
            </div>
        </div>

        <div class="mb-5">
            <h2 class="section-title text-center">What drives us</h2>
            <div class="row g-4 mt-2">
                <div class="col-md-4">
                    <div class="value-card h-100">
                        <i class="bi bi-recycle"></i>
                        <h4 class="fw-bold text-success">Circular economy</h4>
                        <p class="text-muted mb-0">We promote reuse, repair, and recycling to reduce waste and create sustainable value chains.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="value-card h-100">
                        <i class="bi bi-people-fill"></i>
                        <h4 class="fw-bold text-success">Community engagement</h4>
                        <p class="text-muted mb-0">We mobilize citizens, schools, associations, and businesses around shared environmental goals.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="value-card h-100">
                        <i class="bi bi-lightbulb-fill"></i>
                        <h4 class="fw-bold text-success">Innovation</h4>
                        <p class="text-muted mb-0">We combine technology and practical knowledge to make green behaviors easier and more visible.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h2 class="section-title text-center">How it works</h2>
            <div class="row g-4 mt-2">
                <div class="col-md-3">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h5 class="fw-bold text-success">Discover</h5>
                        <p class="text-muted mb-0">Explore collection points, eco-products, and sustainability opportunities nearby.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h5 class="fw-bold text-success">Participate</h5>
                        <p class="text-muted mb-0">Join campaigns, donate, reserve services, or contribute to waste reduction efforts.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h5 class="fw-bold text-success">Track</h5>
                        <p class="text-muted mb-0">Monitor your actions and see how your contribution creates a measurable difference.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <h5 class="fw-bold text-success">Impact</h5>
                        <p class="text-muted mb-0">Help build stronger, cleaner, and more resilient communities for the future.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="cta-box text-center">
            <h3 class="fw-bold text-success mb-3">Ready to make a difference?</h3>
            <p class="text-muted mb-4">Join Biodex and take part in practical actions that protect the environment every day.</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ url('/register') }}" class="btn btn-success btn-lg px-4">Create account</a>
                <a href="{{ url('/contact') }}" class="btn btn-outline-success btn-lg px-4">Contact us</a>
            </div>
        </div>
    </div>
</section>
@endsection
