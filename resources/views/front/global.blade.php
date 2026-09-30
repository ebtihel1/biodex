<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Biodex')</title>

    <!-- ✅ Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: system-ui, sans-serif;
            overflow-x: hidden;
        }

        /* ✅ Navbar transparente au début */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            transition: background-color 0.4s ease, box-shadow 0.3s ease;
            background-color: transparent !important;
            z-index: 1000;
        }

        /* ✅ Quand on scrolle, elle devient verte */
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

        main {
            background: linear-gradient(120deg, #f8fff8, #f5fff2);
            padding-top: 80px; /* espace sous la navbar */
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

    @include('front.navbar')

    <main>
        @yield('content')
    </main>

    <footer>
        © {{ date('Y') }} Biodex — Together for a Cleaner Future 🌱
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('scroll', function () {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
