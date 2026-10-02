<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center bg-white rounded-3 px-2 py-1" href="{{ url('/') }}">
    <img src="{{ asset('images/biodex-logo.png') }}" alt="Biodex" height="32">
</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a href="{{ url('/biodex') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                        <i class="bi bi-house-door me-1"></i>Home
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('front.products.index') }}" class="nav-link {{ request()->routeIs('front.products.*') || request()->is('shop/products*') ? 'active' : '' }}">
                        <i class="bi bi-bag-check me-1"></i>Products
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/biodex/collectionpoints') }}" class="nav-link {{ request()->is('front.collectionpoints.index') ? 'active' : '' }}">
                        <i class="bi bi-geo-alt me-1"></i>collectionpoints
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('/campaignsFront') }}" class="nav-link {{ request()->is('campaigns*') ? 'active' : '' }}">
                        <i class="bi bi-megaphone me-1"></i>Campaigns
                    </a>
                </li>

                <!-- Nouveau lien Waste Categories -->
                <li class="nav-item">
                    <a href="{{ route('front.waste-categories.index') }}" class="nav-link {{ request()->is('waste-categories*') ? 'active' : '' }}">
                        <i class="bi bi-grid-3x3-gap me-1"></i>Waste Categories
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('front.donations.index') }}" class="nav-link {{ request()->routeIs('front.donations.*') ? 'active' : '' }}">
                        <i class="bi bi-heart-fill me-1"></i>Donations
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('front.orders.index') }}" class="nav-link {{ request()->routeIs('front.orders.*') ? 'active' : '' }}">
                        <i class="bi bi-cart me-1"></i>Orders
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('front.reservations.index') }}" class="nav-link {{ request()->routeIs('front.reservations.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-event me-1"></i>Reservations
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('/about') }}" class="nav-link {{ request()->is('about') ? 'active' : '' }}">
                        <i class="bi bi-info-circle me-1"></i>About
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('/contact') }}" class="nav-link {{ request()->is('contact') ? 'active' : '' }}">
                        <i class="bi bi-envelope me-1"></i>Contact
                    </a>
                </li>

               @auth
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-light" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="{{ route('profile.view') }}"><i class="bi bi-person me-1"></i> Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </li>
@else
    <li class="nav-item">
        <a href="{{ url('/login') }}" class="nav-link text-light">
            <i class="bi bi-box-arrow-in-right me-1"></i>Login
        </a>
    </li>
@endauth

            </ul>
        </div>
    </div>
</nav>