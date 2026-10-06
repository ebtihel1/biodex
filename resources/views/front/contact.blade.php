@extends('front.layout')

@section('title', 'Contact')

@section('content')
<style>
    .contact-hero {
        background: linear-gradient(135deg, rgba(6, 38, 25, 0.88), rgba(28, 84, 58, 0.72)),
                    url('{{ asset('images/EarthFront.png') }}') center/cover no-repeat;
        min-height: 360px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #fff;
        padding: 120px 20px 60px;
    }

    .contact-hero h1 {
        font-size: clamp(2.5rem, 4vw, 3.5rem);
        font-weight: 800;
        margin-bottom: 15px;
    }

    .contact-hero p {
        font-size: 1.1rem;
        max-width: 700px;
        margin: 0 auto;
        color: rgba(255,255,255,0.9);
    }

    .contact-card {
        border: none;
        border-radius: 18px;
        background: white;
        box-shadow: 0 12px 28px rgba(25, 135, 84, 0.08);
        padding: 28px 22px;
        height: 100%;
    }

    .contact-card i {
        font-size: 2rem;
        color: #198754;
        margin-bottom: 12px;
    }

    .form-panel {
        background: #fff;
        border-radius: 22px;
        padding: 30px;
        box-shadow: 0 12px 30px rgba(21, 87, 52, 0.08);
    }

    .form-control,
    .form-select,
    .form-control:focus,
    .form-select:focus {
        border-radius: 12px;
        border: 1px solid rgba(25, 135, 84, 0.2);
        box-shadow: none;
    }
</style>

<section class="contact-hero">
    <div class="container">
        <h1>Contact us</h1>
        <p>
            We are here to help you with questions, partnerships, donations, volunteering, or any sustainable initiative you want to start.
        </p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="contact-card text-center">
                    <i class="bi bi-geo-alt-fill"></i>
                    <h5 class="fw-bold text-success">Visit us</h5>
                    <p class="text-muted mb-0">Sidi Bouzid, Tunisia</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="contact-card text-center">
                    <i class="bi bi-envelope-fill"></i>
                    <h5 class="fw-bold text-success">Email</h5>
                    <p class="text-muted mb-0">contact@biodex.tn</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="contact-card text-center">
                    <i class="bi bi-telephone-fill"></i>
                    <h5 class="fw-bold text-success">Phone</h5>
                    <p class="text-muted mb-0">+216 12 345 678</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="contact-card text-center">
                    <i class="bi bi-clock-fill"></i>
                    <h5 class="fw-bold text-success">Hours</h5>
                    <p class="text-muted mb-0">Mon - Sat / 9:00 - 18:00</p>
                </div>
            </div>
        </div>

        <div class="row g-4 align-items-start">
          

            <div class="col-lg-7">
                <div class="form-panel h-100">
                    <h3 class="fw-bold text-success mb-3">Why contact Biodex?</h3>
                    <p class="text-muted mb-4">
                        Whether you want to join a campaign, collaborate with our community, or explore eco-partnership opportunities, we’d love to hear from you.
                    </p>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0 py-3 border-0">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Support for citizens and environmental initiatives
                        </div>
                        <div class="list-group-item px-0 py-3 border-0">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Partnerships with schools, NGOs, and businesses
                        </div>
                        <div class="list-group-item px-0 py-3 border-0">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Guidance for donations, volunteering, and collection programs
                        </div>
                        <div class="list-group-item px-0 py-3 border-0">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Answers about sustainable products and local recycling actions
                        </div>
                    </div>

                    <div class="mt-4 rounded-4 p-4 bg-success-subtle border border-success-subtle">
                        <p class="fw-bold text-success mb-1">Need a quick answer?</p>
                        <p class="mb-0 text-muted">Reach us directly at <strong>contact@biodex.tn</strong> or call <strong>+216 12 345 678</strong>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
