<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Invoice System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        /* Navbar Animations */
        .navbar {
            animation: slideDown 0.8s ease-out;
            position: relative;
            overflow: hidden;
        }

        /* Navbar background gradient animation */
        .navbar::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: shimmer 3s infinite;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes shimmer {
            0% {
                left: -100%;
            }

            100% {
                left: 100%;
            }
        }

        /* Brand animation */
        .navbar-brand {
            animation: fadeInScale 1s ease-out 0.3s both;
            position: relative;
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.8);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Brand icon rotation on hover */
        .navbar-brand i {
            transition: transform 0.5s ease;
            display: inline-block;
        }

        .navbar-brand:hover i {
            transform: rotate(360deg);
        }

        /* Brand text glow effect */
        .navbar-brand span {
            position: relative;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
            transition: text-shadow 0.3s ease;
        }

        .navbar-brand:hover span {
            text-shadow: 0 0 20px rgba(255, 255, 255, 0.6);
        }

        /* Nav items stagger animation */
        .navbar-nav .nav-item {
            animation: fadeInUp 0.6s ease-out both;
            position: relative;
        }

        .navbar-nav .nav-item:nth-child(1) {
            animation-delay: 0.5s;
        }

        .navbar-nav .nav-item:nth-child(2) {
            animation-delay: 0.7s;
        }

        .navbar-nav .nav-item:nth-child(3) {
            animation-delay: 0.9s;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Nav link hover effect */
        .navbar-nav .nav-link {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            border-radius: 8px;
            padding: 8px 16px !important;
        }

        /* Underline animation */
        .navbar-nav .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: white;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .navbar-nav .nav-link:hover::before {
            width: 80%;
        }

        /* Background slide effect */
        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            transition: left 0.4s ease;
            z-index: -1;
        }

        .navbar-nav .nav-link:hover::after {
            left: 0;
        }

        /* Icon animation on hover */
        .navbar-nav .nav-link i {
            transition: transform 0.3s ease;
            display: inline-block;
        }

        .navbar-nav .nav-link:hover i {
            transform: translateX(-3px) scale(1.1);
        }

        /* Active link indicator */
        .navbar-nav .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            font-weight: 600;
        }

        .navbar-nav .nav-link.active::before {
            width: 80%;
        }

        /* Navbar toggler animation */
        .navbar-toggler {
            transition: transform 0.3s ease;
            border: none;
            padding: 8px;
        }

        .navbar-toggler:hover {
            transform: rotate(90deg);
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.3);
        }

        /* Pulse animation for navbar shadow */
        @keyframes shadowPulse {

            0%,
            100% {
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            50% {
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            }
        }

        .navbar.shadow {
            animation: shadowPulse 3s ease-in-out infinite;
        }

        /* Responsive adjustments */
        @media (max-width: 991px) {
            .navbar-nav .nav-item {
                animation: fadeInLeft 0.4s ease-out both;
            }

            @keyframes fadeInLeft {
                from {
                    opacity: 0;
                    transform: translateX(-20px);
                }

                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
        }
    </style>
</head>

<body class="bg-light">

    <!-- Navigations -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <i class="fas fa-file-invoice text-white fs-4 me-3"></i>
                <span class="text-white fs-4 fw-bold">Invoice System</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('dashboard') }}">
                            <i class="fas fa-home me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('clients.index') }}">
                            <i class="fas fa-users me-1"></i> Clients
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('invoices.index') }}">
                            <i class="fas fa-file-invoice-dollar me-1"></i> Invoices
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Contents -->
    <main class="container py-5">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white mt-5 py-5 border-top">
        <div class="container text-center text-muted">
            <p class="mb-0">&copy; 2025 iCreativez Technologies. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 Configurations -->
    <script>
        // Configure SweetAlert2 defaults
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Success message handler
        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            });
        @endif

        // Confirmation function for delete actions
        function confirmDelete(message, callback) {
            Swal.fire({
                title: 'Are you sure?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    callback();
                }
            });
        }

        // Add active class to current page nav link
        document.addEventListener('DOMContentLoaded', function() {
            const currentLocation = window.location.href;
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

            navLinks.forEach(link => {
                if (link.href === currentLocation) {
                    link.classList.add('active');
                }
            });
        });
    </script>
    </document_content>
