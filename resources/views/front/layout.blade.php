<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biodex Dashboard - {{ $title ?? 'Admin' }}</title>

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: system-ui, sans-serif;
            overflow-x: hidden;
        }

        /* NAVBAR transparente au début */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            transition: background-color 0.4s ease, box-shadow 0.3s ease;
            background-color: transparent !important;
            z-index: 1000;
        }

        /* Quand on scrolle, elle devient verte */
        .navbar.scrolled {
            background-color: #198754 !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .navbar-brand {
            font-weight: bold;
            color: #fff !important;
        }

        .nav-link {
            color: #fff !important;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            text-decoration: underline;
            color: #a7f3d0 !important;
        }


        .hero-header {
    height: 80vh;
    background: url("{{ asset('images/Earth.png') }}") no-repeat center center/cover;
    position: relative;
    display: flex;
    align-items: center;
    /* ↓ On aligne le contenu à gauche */
    justify-content: flex-start;
    color: white;
    padding-left: 100px; /* décale le texte à peu près au niveau du logo */
}

.hero-header::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
}

.hero-content {
    position: relative;
    z-index: 2;
    max-width: 600px; /* limite la largeur pour une belle mise en page */
}

.hero-content h1 {
    font-size: 2.8rem;
    font-weight: bold;
}

.hero-content span {
    color: #a7f3d0; /* vert clair accentué */
}
       
     

        main {
            background: linear-gradient(120deg, #f8fff8, #f5fff2);
            padding-top: 80px; /* espace sous navbar */
        }

        footer {
            background-color: #198754;
            color: #fff;
            text-align: center;
            padding: 10px 0;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

@extends('front.navbar')


    <section class="hero-header">
    <div class="hero-content text-start">
        <h1>The <span>Biodex</span> Platform for a Cleaner Future</h1>
        <p>
            Biodex connects citizens, recyclers, and companies to turn waste into new opportunities — 
            for people, planet, and progress 🌍.
        </p>
        <a href="{{ url('/register') }}" class="btn btn-success me-2">Join Now</a>
        <a href="{{ route('front.products.index') }}" class="btn btn-outline-light">Explore Products</a>
    </div>
    </section>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">Home</a></li>
                    <li class="nav-item"><a href="{{ route('front.products.index') }}" class="nav-link {{ request()->routeIs('front.products.*') || request()->is('shop/products*') ? 'active' : '' }}">Products</a></li>
                    <li class="nav-item"><a href="{{ url('/biodex/collectionpoints') }}" class="nav-link {{ request()->is('biodex/collectionpoints*') ? 'active' : '' }}">Points de collecte</a></li>
                    <li class="nav-item"><a href="{{ route('front.wastes.index') }}" class="nav-link {{ request()->routeIs('front.wastes.*') ? 'active' : '' }}">Wastes</a></li>
                    <li class="nav-item"><a href="{{ url('/waste-categories') }}" class="nav-link {{ request()->is('waste-categories*') ? 'active' : '' }}">Waste Categories</a></li>

                    <li class="nav-item"><a href="{{ route('front.donations.index') }}" class="nav-link {{ request()->routeIs('front.donations.*') ? 'active' : '' }}">Donations</a></li>
                    <li class="nav-item"><a href="{{ route('front.orders.index') }}" class="nav-link {{ request()->routeIs('front.orders.*') ? 'active' : '' }}">Orders</a></li>
                    <li class="nav-item"><a href="{{ route('front.reservations.index') }}" class="nav-link {{ request()->routeIs('front.reservations.*') ? 'active' : '' }}">Reservations</a></li>
                    <li class="nav-item"><a href="{{ url('/contact') }}" class="nav-link {{ request()->is('contact') ? 'active' : '' }}">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>



    <!-- Main content -->
    <main class="container-fluid py-5">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        © {{ date('Y') }} Biodex — Together for a Cleaner Future 🌱
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
        // Navbar Scroll Effect
        document.addEventListener('scroll', function () {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });</script>



</body>

</html>