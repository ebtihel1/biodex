@extends('front.layout')

@section('content')
<style>
    /* Section Hero plein écran avec image de fond */
    .hero {
        position: relative;
        height: 100vh;
        background: url("{{ asset('images/Earth Day Banner.png') }}") no-repeat center center/cover;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-align: center;
        overflow: hidden;
    }

    /* Overlay sombre pour lisibilité du texte */
    .hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
    }

    /* Texte centré au milieu */
    .hero-content {
        position: relative;
        z-index: 2;
        max-width: 700px;
        padding: 20px;
    }

    .hero-content h1 {
        font-size: 3rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .hero-content h1 span {
        color: #a7f3d0;
        background: rgba(0, 0, 0, 0.2);
        padding: 5px 10px;
        border-radius: 8px;
    }

    .hero-content p {
        margin-top: 15px;
        font-size: 1.25rem;
        color: #e8f5e9;
    }

    .hero-content .btn {
        margin-top: 25px;
        padding: 12px 28px;
        font-weight: 600;
        border-radius: 8px;
    }

    .btn-success {
        background-color: #2d6a4f;
        border: none;
    }

    .btn-outline-light {
        border: 2px solid #fff;
        color: #fff;
        margin-left: 10px;
    }

    /* Cartes sous le hero */
    .features {
        padding: 50px 0;
    }

    .features .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s;
    }

    .features .card:hover {
        transform: translateY(-5px);
    }

    @media (max-width: 768px) {
        .hero-content h1 {
            font-size: 2.2rem;
        }
        .hero-content p {
            font-size: 1.1rem;
        }
    }
</style>



<!-- Section Features -->
<section class="features">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="card h-100 p-4">
                    <i class="bi bi-recycle text-success display-5 mb-3"></i>
                    <h5 class="fw-bold text-success">Recycling</h5>
                    <p class="text-muted">Give waste a new life through smart, sustainable recycling methods.</p>
                         <a href="{{ url('/wastess') }}" class="btn btn-success">Join Now</a>

                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 p-4">
                    <i class="bi bi-bag-heart text-success display-5 mb-3"></i>
                    <h5 class="fw-bold text-success">Eco Products</h5>
                    <p class="text-muted">Discover beautiful, useful, and affordable recycled products.</p>
                         <a href="{{ url('/register') }}" class="btn btn-success">Join Now</a>

                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 p-4">
                    <i class="bi bi-people text-success display-5 mb-3"></i>
                    <h5 class="fw-bold text-success">Community</h5>
                    <p class="text-muted">Join a movement of citizens and businesses working for a green world.</p>
                         <a href="{{ url('/campaignsFront') }}" class="btn btn-success">Join Now</a>

                </div>
            </div>
        </div>
    </div>
</section>
@endsection
