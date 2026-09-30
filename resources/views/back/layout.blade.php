<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Back Office - Biodex</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
    body {
        transition: all 0.3s;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    /* Sidebar */
    .sidebar {
        width: 250px;
        min-height: 100vh;
        background: linear-gradient(180deg, #003d39ff 0%, #000b0aff 100%);
        transition: width 0.3s ease;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1100;
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.2);
    }
    .sidebar.collapsed { width: 70px; }

    .sidebar .nav-link {
        color: #e6f4f3;
        padding: 12px 15px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        transition: background-color 0.3s, transform 0.2s, padding 0.3s;
        position: relative;
        font-size: 1rem;
        font-weight: 500;
    }
    .sidebar .nav-link:hover, .sidebar .nav-link.active {
        background-color: rgba(255, 255, 255, 0.1);
        transform: scale(1.02);
    }

    .sidebar .nav-text {
        transition: opacity 0.3s, display 0.3s;
        margin-left: 12px;
    }
    .sidebar.collapsed .nav-text {
        opacity: 0;
        display: none;
    }

    .sidebar .nav-item .submenu {
        display: none;
        list-style: none;
        padding-left: 30px;
        background-color: rgba(0, 0, 0, 0.15);
        border-radius: 8px;
        margin: 8px 0;
    }
    .sidebar .nav-item.show .submenu {
        display: block;
    }
    .sidebar .submenu .nav-link {
        font-size: 0.875rem;
        padding: 8px 15px;
        color: #d1e8e6;
    }
    .sidebar .submenu .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }

    /* Tooltip */
    /* Tooltips for collapsed state */
    .sidebar.collapsed .nav-link::after {
        content: attr(data-tooltip);
        position: absolute;
        left: 80px;
        background-color: #0d403d;
        color: #e6f4f3;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.875rem;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease-in-out;
        z-index: 1200;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    .sidebar.collapsed .nav-link:hover::after {
        opacity: 1;
    }

    /* Toggle button */
    /* Toggle button animation */
    .sidebar .toggle-btn {
        transition: transform 0.3s ease, background-color 0.3s;
        background-color: #2aa198;
        border: none;
    }
    .sidebar .toggle-btn:hover {
        background-color: #3cb8ae;
    }
    .sidebar.collapsed .toggle-btn {
        transform: rotate(180deg);
    }

    /* Main content */
    .main-content {
        margin-left: 250px;
        width: calc(100% - 250px);
        transition: all 0.3s;
    }
    .main-content.collapsed {
        margin-left: 70px;
        width: calc(100% - 70px);
    }

    /* Header */
    .header {
        background-color: #fff;
        padding: 0.5rem 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    /* Cards */
    /* Card style */
    .card {
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    .card:hover { transform: translateY(-5px); }

    @media (max-width: 768px) {
        .sidebar {
            width: 70px;
        }
        .main-content {
            margin-left: 70px;
            width: calc(100% - 70px);
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
    }
</style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3" id="sidebar">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="text-white mb-0 nav-text">🌿 Biodex</h4>
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
                    <a href="#" class="nav-link" data-tooltip="Catégories" aria-label="Catégories"><i class="bi bi-tags me-2"></i> <span class="nav-text">Catégories</span></a>
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
                <div class="d-flex align-items-center">
                    <input type="text" class="form-control form-control-sm me-2" placeholder="Search..." aria-label="Search">
                    <i class="bi bi-bell fs-4 me-3" aria-label="Notifications"></i>
                    <i class="bi bi-person-circle fs-4" aria-label="User Profile"></i>
                </div>
            </div>

            <!-- Content -->
            <div class="p-4">
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