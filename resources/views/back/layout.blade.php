<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Back Office - Biodex</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
    :root {
        --biodex-dark: #032e2b;
        --biodex-mid: #0b4d42;
        --biodex-soft: #eafaf4;
        --biodex-primary: #1fa26a;
        --biodex-primary-strong: #137d52;
        --biodex-text: #153f3a;
        --biodex-muted: #68817c;
        --panel-bg: rgba(255, 255, 255, 0.75);
        --shadow-soft: 0 14px 30px rgba(8, 46, 39, 0.08);
        --shadow-card: 0 10px 24px rgba(13, 63, 52, 0.08);
    }

    body {
        transition: all 0.3s;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: linear-gradient(180deg, #f4faf7 0%, #edf5f1 100%);
        color: var(--biodex-text);
    }

    /* Sidebar */
    .sidebar {
        width: 250px;
        min-height: 100vh;
        background: linear-gradient(180deg, #053b38 0%, #021d1b 100%);
        transition: width 0.3s ease;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1100;
        box-shadow: 18px 0 35px rgba(2, 28, 25, 0.18);
        border-right: 1px solid rgba(255,255,255,0.05);
    }
    .sidebar.collapsed { width: 76px; }

    .sidebar .nav-link {
        color: rgba(230, 244, 243, 0.9);
        padding: 11px 14px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        transition: background-color 0.25s ease, transform 0.2s ease, box-shadow 0.25s ease, color 0.25s ease;
        position: relative;
        font-size: 0.96rem;
        font-weight: 500;
        margin-bottom: 4px;
    }
    .sidebar .nav-link:hover {
        background: linear-gradient(90deg, rgba(33, 162, 110, 0.22), rgba(255,255,255,0.08));
        transform: translateX(2px);
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.04);
        color: #ffffff;
    }

    /* ===== Lien actif — couleur lumineuse ===== */
    .sidebar .nav-link.active,
    .sidebar .nav-item.show > .nav-link {
        background: linear-gradient(90deg, rgba(43, 191, 141, 0.45), rgba(43, 191, 141, 0.15));
        color: #ffffff !important;
        font-weight: 600;
        box-shadow:
            inset 0 0 0 1px rgba(43, 191, 141, 0.5),
            0 0 12px rgba(43, 191, 141, 0.35);
        transform: translateX(3px);
    }

    /* Barre lumineuse à gauche du lien actif */
    .sidebar .nav-link.active::before,
    .sidebar .nav-item.show > .nav-link::before {
        content: "";
        position: absolute;
        left: 0;
        top: 10%;
        bottom: 10%;
        width: 3px;
        border-radius: 3px;
        background: linear-gradient(180deg, #2abf8d, #1fa26a);
        box-shadow: 0 0 10px rgba(43, 191, 141, 0.8);
    }

    /* Icône lumineuse pour le lien actif */
    .sidebar .nav-link.active > i,
    .sidebar .nav-item.show > .nav-link > i {
        color: #4ff0b7 !important;
        filter: drop-shadow(0 0 6px rgba(79, 240, 183, 0.7));
    }

    .sidebar .nav-text {
        transition: opacity 0.3s ease, display 0.3s ease;
        margin-left: 12px;
        white-space: nowrap;
    }
    .sidebar.collapsed .nav-text {
        opacity: 0;
        display: none;
    }

    .sidebar .nav-item .submenu {
        display: none;
        list-style: none;
        padding: 10px 10px 6px 10px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 12px;
        margin: 8px 0 0 0;
        border: 1px solid rgba(255,255,255,0.04);
    }
    .sidebar .nav-item.show .submenu {
        display: block;
    }
    .sidebar .submenu .nav-link {
        font-size: 0.84rem;
        padding: 8px 10px;
        color: #dfeae8;
    }
    .sidebar .submenu .nav-link:hover {
        background-color: rgba(255,255,255,0.05);
    }

    /* Sous-liens actifs */
    .sidebar .submenu .nav-link.active {
        background: rgba(43, 191, 141, 0.2);
        color: #4ff0b7 !important;
        font-weight: 600;
        box-shadow: inset 0 0 0 1px rgba(43, 191, 141, 0.3);
    }

    .sidebar.collapsed .nav-link::after {
        content: attr(data-tooltip);
        position: absolute;
        left: 72px;
        background-color: #0e3a37;
        color: #e6f4f3;
        padding: 7px 12px;
        border-radius: 8px;
        font-size: 0.76rem;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease-in-out;
        z-index: 1200;
        box-shadow: 0 8px 18px rgba(0,0,0,0.2);
    }
    .sidebar.collapsed .nav-link:hover::after {
        opacity: 1;
    }

    .sidebar .toggle-btn {
        transition: transform 0.3s ease, background-color 0.3s;
        background: linear-gradient(135deg, #2abf8d, #1d8d68);
        border: none;
        color: #fff;
        border-radius: 10px;
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 18px rgba(25, 133, 94, 0.25);
        flex-shrink: 0;
    }
    .sidebar .toggle-btn:hover {
        background: linear-gradient(135deg, #37ce9e, #236f56);
    }
    .sidebar.collapsed .toggle-btn {
        transform: rotate(180deg);
    }

    /* ===== Logo de la sidebar ===== */
    .sidebar-brand {
        overflow: hidden;
        transition: opacity 0.25s ease, width 0.3s ease, margin 0.3s ease;
        display: flex;
        align-items: center;
    }

    .sidebar-logo {
        height: 36px;
        width: auto;
        max-width: 120px;
        object-fit: contain;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.08);
        padding: 4px;
        transition: opacity 0.25s ease, transform 0.3s ease;
    }

    .sidebar.collapsed .sidebar-brand {
        opacity: 0;
        width: 0;
        margin: 0;
        pointer-events: none;
    }

    .sidebar.collapsed .sidebar-logo {
        opacity: 0;
        transform: translateX(-10px);
    }

    /* Main content */
    .main-content {
        margin-left: 250px;
        width: calc(100% - 250px);
        transition: all 0.3s;
        min-height: 100vh;
        padding: 16px 22px 24px;
    }
    .main-content.collapsed {
        margin-left: 76px;
        width: calc(100% - 76px);
    }

    /* Header */
    .header {
        background: rgba(255,255,255,0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(18, 63, 55, 0.08);
        border-radius: 18px;
        padding: 0.9rem 1.1rem;
        box-shadow: var(--shadow-soft);
        position: sticky;
        top: 16px;
        z-index: 1000;
        margin-bottom: 22px;
    }

    .header h5 {
        color: var(--biodex-text);
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .header-profile {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: linear-gradient(135deg, #eafaf4, #dff6ec);
        border: 1px solid rgba(31, 162, 106, 0.15);
        color: var(--biodex-primary-strong);
        box-shadow: 0 8px 18px rgba(31, 162, 106, 0.12);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .header-profile:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 22px rgba(31, 162, 106, 0.18);
    }

    /* Cards */
    .card {
        border-radius: 16px;
        box-shadow: var(--shadow-card);
        border: 1px solid rgba(18, 63, 55, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background: rgba(255,255,255,0.9);
    }
    .card:hover { transform: translateY(-4px); }

    .content-shell {
        background: rgba(255,255,255,0.45);
        border: 1px solid rgba(18, 63, 55, 0.04);
        border-radius: 22px;
        padding: 20px;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.7);
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 76px;
        }
        .main-content {
            margin-left: 76px;
            width: calc(100% - 76px);
            padding: 12px 12px 16px;
        }
        .sidebar .nav-text {
            opacity: 0;
            display: none;
        }
        .sidebar.collapsed .nav-link::after {
            display: none;
        }
        .sidebar .submenu {
            padding-left: 15px;
        }
        .header {
            gap: 12px;
            flex-wrap: wrap;
        }
        .sidebar-brand {
            opacity: 0;
            width: 0;
            overflow: hidden;
            pointer-events: none;
        }
    }
</style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3" id="sidebar">
            <div class="d-flex align-items-center justify-content-between mb-4 px-1">
                <div class="d-flex align-items-center gap-2 sidebar-brand">
                    <img src="{{ asset('images/biodex-logo.png') }}" alt="Biodex" class="sidebar-logo">
                </div>
                <button class="btn btn-sm btn-light toggle-btn" id="toggleSidebar" aria-label="Toggle sidebar"><i class="bi bi-list"></i></button>
            </div>

            <ul class="nav flex-column" role="navigation">
                {{-- Dashboard --}}
                <li class="nav-item mb-2">
                    <a href="{{ url('back/home') }}" class="nav-link {{ request()->is('back/home') ? 'active' : '' }}" data-tooltip="Dashboard" aria-label="Dashboard">
                        <i class="bi bi-speedometer2 me-2"></i> <span class="nav-text">Dashboard</span>
                    </a>
                </li>

                {{-- Waste Management --}}
                <li class="nav-item mb-2 {{ request()->routeIs('wastes.*') || request()->routeIs('waste_categories.*') ? 'show' : '' }}">
                    <a href="#wasteSubmenu" class="nav-link {{ request()->routeIs('wastes.*') || request()->routeIs('waste_categories.*') ? 'active' : '' }}" onclick="toggleSubmenu(event)" data-tooltip="Waste Management" aria-label="Waste Management">
                        <i class="bi bi-trash3 me-2"></i> <span class="nav-text">Waste Management</span>
                    </a>
                    <ul class="submenu list-unstyled" id="wasteSubmenu">
                        <li>
                            <a href="{{ route('wastes.index') }}" class="nav-link text-white {{ request()->routeIs('wastes.*') ? 'active' : '' }}" data-tooltip="All Waste">
                                <i class="bi bi-recycle me-2"></i> All Waste
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('waste_categories.index') }}" class="nav-link text-white {{ request()->routeIs('waste_categories.*') ? 'active' : '' }}" data-tooltip="Waste Categories">
                                <i class="bi bi-tags me-2"></i> Waste Categories
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Collection Points --}}
                <li class="nav-item mb-2 {{ request()->is('dashbored/collectionpoints') || request()->is('collectionpoints/*') ? 'show' : '' }}">
                    <a href="#collectionSubmenu" class="nav-link {{ request()->is('dashbored/collectionpoints') || request()->is('collectionpoints/*') ? 'active' : '' }}" onclick="toggleSubmenu(event)" data-tooltip="Collection Points" aria-label="Collection Points">
                        <i class="bi bi-geo-alt me-2"></i> <span class="nav-text">Collection Points</span>
                    </a>
                    <ul class="submenu list-unstyled" id="collectionSubmenu">
                        <li>
                            <a href="{{ url('/dashbored/collectionpoints') }}" class="nav-link {{ request()->is('dashbored/collectionpoints') ? 'active' : '' }}">
                                <i class="bi bi-geo-alt me-2"></i> Collection Points
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/collectionpoints/predictions') }}" class="nav-link {{ request()->is('collectionpoints/predictions') ? 'active' : '' }}">
                                <i class="bi bi-cpu me-1"></i> IA Points de Collecte
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Recycling & Products --}}
                <li class="nav-item mb-2 {{ request()->routeIs('recyclingprocesses.*') || request()->routeIs('products.*') ? 'show' : '' }}">
                    <a href="#recyclingSubmenu" class="nav-link {{ request()->routeIs('recyclingprocesses.*') || request()->routeIs('products.*') ? 'active' : '' }}" onclick="toggleSubmenu(event)" data-tooltip="Recycling & Products" aria-label="Recycling & Products">
                        <i class="bi bi-arrow-repeat me-2"></i> <span class="nav-text">Recycling & Products</span>
                    </a>
                    <ul class="submenu list-unstyled" id="recyclingSubmenu">
                        <li>
                            <a href="{{ route('recyclingprocesses.index') }}" class="nav-link {{ request()->routeIs('recyclingprocesses.*') ? 'active' : '' }}">
                                <i class="bi bi-arrow-repeat me-2"></i> Recycling Processes
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                                <i class="bi bi-box-seam me-2"></i> Recycled Products
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Transactions --}}
                <li class="nav-item mb-2 {{ request()->routeIs('back.donations.*') || request()->routeIs('back.orders.*') || request()->routeIs('back.reservations.*') ? 'show' : '' }}">
                    <a href="#transactionsSubmenu" class="nav-link {{ request()->routeIs('back.donations.*') || request()->routeIs('back.orders.*') || request()->routeIs('back.reservations.*') ? 'active' : '' }}" onclick="toggleSubmenu(event)" data-tooltip="Transactions" aria-label="Transactions">
                        <i class="bi bi-credit-card me-2"></i> <span class="nav-text">Transactions</span>
                    </a>
                    <ul class="submenu list-unstyled" id="transactionsSubmenu">
                        <li>
                            <a href="{{ route('back.donations.index') }}" class="nav-link {{ request()->routeIs('back.donations.*') ? 'active' : '' }}" data-tooltip="Donations">
                                <i class="bi bi-heart-fill me-2"></i> Donations
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('back.orders.index') }}" class="nav-link {{ request()->routeIs('back.orders.*') ? 'active' : '' }}" data-tooltip="Orders">
                                <i class="bi bi-cart me-2"></i> Orders
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('back.reservations.index') }}" class="nav-link {{ request()->routeIs('back.reservations.*') ? 'active' : '' }}" data-tooltip="Reservations">
                                <i class="bi bi-calendar-event me-2"></i> Reservations
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Campaign Management --}}
                <li class="nav-item mb-2">
                    <a href="{{ route('back.campaigns') }}" class="nav-link {{ request()->routeIs('back.campaigns') || request()->is('back/home/campaigns*') ? 'active' : '' }}" data-tooltip="Campaign Management" aria-label="Campaign Management">
                        <i class="bi bi-megaphone me-2"></i> <span class="nav-text">Campaign Management</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main content -->
        <div class="main-content" id="mainContent">
            <!-- Header -->
            <div class="header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"></h5>
                <div class="d-flex align-items-center gap-3">
                    @include('partials.notifications')
                    <a href="{{ route('back.profile') }}" class="header-profile text-decoration-none" aria-label="User Profile" title="Profile">
                        <i class="bi bi-person-circle fs-4"></i>
                    </a>
                </div>
            </div>

            <!-- Content -->
            <div class="content-shell">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleSidebarBtn = document.getElementById('toggleSidebar');

        // Toggle sidebar
        toggleSidebarBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
            const isExpanded = !sidebar.classList.contains('collapsed');
            toggleSidebarBtn.setAttribute('aria-expanded', isExpanded);
        });

        // Toggle submenu
        function toggleSubmenu(e) {
            e.preventDefault();
            const parent = e.target.closest('.nav-item');
            const isCollapsed = sidebar.classList.contains('collapsed');
            const submenu = parent.querySelector('.submenu');
            const isOpen = parent.classList.contains('show');

            document.querySelectorAll('.nav-item.show').forEach(item => {
                if (item !== parent) item.classList.remove('show');
            });

            parent.classList.toggle('show');

            if (isCollapsed && !isOpen) {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('collapsed');
                toggleSidebarBtn.setAttribute('aria-expanded', true);
            }

            const link = parent.querySelector('.nav-link');
            link.setAttribute('aria-expanded', !isOpen);
        }

        // Keyboard navigation
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    if (link.getAttribute('href') === '#') {
                        toggleSubmenu(e);
                    } else {
                        window.location.href = link.getAttribute('href');
                    }
                }
            });
        });
    </script>
</body>
</html>