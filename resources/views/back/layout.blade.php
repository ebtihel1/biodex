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
    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
        background: linear-gradient(90deg, rgba(33, 162, 110, 0.22), rgba(255,255,255,0.08));
        transform: translateX(2px);
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.04);
        color: #ffffff;
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
    }
    .sidebar .toggle-btn:hover {
        background: linear-gradient(135deg, #37ce9e, #236f56);
    }
    .sidebar.collapsed .toggle-btn {
        transform: rotate(180deg);
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

    .header-search {
        width: 220px;
        border: 1px solid rgba(18, 63, 55, 0.1);
        background: #f4faf7;
        border-radius: 12px;
        padding: 0.6rem 0.9rem;
        color: var(--biodex-text);
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
        .header-search {
            width: 150px;
        }
    }
</style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3" id="sidebar">
            <div class="d-flex align-items-center justify-content-between mb-4 px-1">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('images/biodex-logo.png') }}" alt="Biodex" style="height: 36px; width: auto; max-width: 120px; object-fit: contain; border-radius: 8px; background: rgba(255,255,255,0.08); padding: 4px;">
                </div>
                <button class="btn btn-sm btn-light toggle-btn" id="toggleSidebar" aria-label="Toggle sidebar"><i class="bi bi-list"></i></button>
            </div>

            <ul class="nav flex-column" role="navigation">
                <li class="nav-item mb-2">
                    <a href="{{ url('back/home') }}" class="nav-link" data-tooltip="Dashboard" aria-label="Dashboard"><i class="bi bi-speedometer2 me-2"></i> <span class="nav-text">Dashboard</span></a>
                </li>
                <li class="nav-item mb-2">
                    <a href="#wasteSubmenu" class="nav-link" onclick="toggleSubmenu(event)" data-tooltip="Waste Management" aria-label="Waste Management"><i class="bi bi-trash3 me-2"></i> <span class="nav-text">Waste Management</span></a>
                  <ul class="submenu list-unstyled" id="wasteSubmenu">
    <li>
        <a href="{{ route('wastes.index') }}" class="nav-link text-white" data-tooltip="All Waste">
            <i class="bi bi-recycle me-2"></i> All Waste
        </a>
    </li>
    <li>
         <a href="{{ route('waste_categories.index') }}" class="nav-link text-white" data-tooltip="Waste Categories">
            <i class="bi bi-tags me-2"></i> Waste Categories
        </a>
    </li>
    <li>
        <a href="{{ route('predictwaste') }}" class="nav-link text-white" data-tooltip="AI Waste Prediction">
                <i class="bi bi-cpu me-2"></i> Predict Waste
            </a>
    </li>
    <li>
         <a href="{{ route('ai.advice.form') }}" class="nav-link text-white" data-tooltip="AI Recycling Advice">
                <i class="bi bi-lightbulb me-2"></i> AI Recycling Advice
            </a>
    </li>
</ul>
               
</li>
 <!-- NEW: Collection Points -->
                <li class="nav-item mb-2">
                    <a href="#collectionSubmenu" class="nav-link" onclick="toggleSubmenu(event)" data-tooltip="Collection Points" aria-label="Collection Points">
                        <i class="bi bi-geo-alt me-2"></i> <span class="nav-text">Collection Points</span>
                    </a>
                    <ul class="submenu list-unstyled" id="collectionSubmenu">
                        <li>
                            <a href="{{ url('/dashbored/collectionpoints') }}" class="nav-link {{ request()->is('collectionpoints/index') ? 'active' : '' }}">
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


                <!-- NEW: Recycling & Products Section -->
                <li class="nav-item mb-2">
                    <a href="#recyclingSubmenu" class="nav-link" onclick="toggleSubmenu(event)" data-tooltip="Recycling & Products" aria-label="Recycling & Products">
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
                        <li>
                            <a href="{{ route('ai.recycling.demo') }}" class="nav-link {{ request()->routeIs('ai.recycling.*') ? 'active' : '' }}">
                                <i class="bi bi-robot me-2"></i> AI Recycling
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- NEW: Transactions Section -->
                <li class="nav-item mb-2">
                    <a href="#transactionsSubmenu" class="nav-link" onclick="toggleSubmenu(event)" data-tooltip="Transactions" aria-label="Transactions">
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

               
                <li class="nav-item mb-2">
                    <a href="{{ route('back.campaigns') }}" class="nav-link" data-tooltip="Campaign Management" aria-label="Campaign Management"><i class="bi bi-megaphone me-2"></i> <span class="nav-text">Campaign Management</span></a>
                </li>
            </ul>
        </div>

        <!-- Main content -->
        <div class="main-content" id="mainContent">
            <!-- Header -->
            <div class="header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Dashboard</h5>
                <div class="d-flex align-items-center gap-3">
                    <input type="text" class="header-search form-control form-control-sm" placeholder="Search..." aria-label="Search">
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
            // Update aria-expanded for accessibility
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

            // Close all other submenus
            document.querySelectorAll('.nav-item.show').forEach(item => {
                if (item !== parent) item.classList.remove('show');
            });

            // Toggle the submenu
            parent.classList.toggle('show');

            // If sidebar is collapsed, temporarily expand it to show submenu
            if (isCollapsed && !isOpen) {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('collapsed');
                toggleSidebarBtn.setAttribute('aria-expanded', true);
            }

            // Update aria-expanded for accessibility
            const link = parent.querySelector('.nav-link');
            link.setAttribute('aria-expanded', !isOpen);
        }

        // Keyboard navigation for accessibility
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