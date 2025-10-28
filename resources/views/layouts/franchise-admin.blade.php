<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Franchise Panel</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <style>
        /* PROFESSIONAL FRANCHISE THEME - Deep Green/Teal with High Contrast */

        /* Sidebar - Deep Professional Green */
        .main-sidebar {
            background: linear-gradient(180deg, #1e7e34 0%, #28a745 100%) !important;
        }

        /* Brand Link - Darker Green for Better Contrast */
        .brand-link {
            background: linear-gradient(45deg, #155724, #1e7e34) !important;
            border-bottom: 1px solid rgba(255,255,255,0.1) !important;
        }

        /* Active Navigation Items */
        .nav-sidebar .nav-item > .nav-link.active {
            background: linear-gradient(45deg, #155724, #1c7430) !important;
            color: #ffffff !important;
            border-radius: 5px !important;
            margin: 2px 10px !important;
        }

        /* Navigation Icons - High Contrast Colors */
        .nav-sidebar .nav-link {
            color: rgba(255,255,255,0.9) !important;
            transition: all 0.3s ease !important;
            margin: 1px 5px !important;
            border-radius: 5px !important;
        }

        .nav-sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1) !important;
            color: #ffffff !important;
        }

        /* Icon Colors for Better Visibility */
        .nav-sidebar .fa-tachometer-alt { color: #ffc107 !important; }
        .nav-sidebar .fa-users { color: #17a2b8 !important; }
        .nav-sidebar .fa-book { color: #fd7e14 !important; }
        .nav-sidebar .fa-certificate { color: #dc3545 !important; }
        .nav-sidebar .fa-credit-card { color: #6f42c1 !important; }
        .nav-sidebar .fa-chart-bar { color: #20c997 !important; }
        .nav-sidebar .fa-cog { color: #6c757d !important; }

        /* Header Text Colors */
        .nav-header {
            color: rgba(255,255,255,0.7) !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            font-size: 11px !important;
            letter-spacing: 0.5px !important;
        }

        /* User Panel Improvements */
        .user-panel .info a {
            color: #ffffff !important;
            font-weight: 500 !important;
        }

        .user-panel small {
            color: rgba(255,255,255,0.8) !important;
        }

        /* Content Area Improvements */
        .content-wrapper {
            background: #f4f6f9 !important;
        }

        /* Navbar Enhancements */
        .main-header.navbar {
            border-bottom: 1px solid #dee2e6 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04) !important;
        }

        /* Card Enhancements */
        .card {
            border-radius: 10px !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08) !important;
            border: none !important;
        }

        /* Small Box Custom Colors for Better Readability */
        .small-box.bg-info {
            background: linear-gradient(45deg, #17a2b8, #20c997) !important;
            color: #ffffff !important;
        }

        .small-box.bg-success {
            background: linear-gradient(45deg, #28a745, #20c997) !important;
            color: #ffffff !important;
        }

        .small-box.bg-warning {
            background: linear-gradient(45deg, #ffc107, #fd7e14) !important;
            color: #ffffff !important;
        }

        .small-box.bg-danger {
            background: linear-gradient(45deg, #dc3545, #e83e8c) !important;
            color: #ffffff !important;
        }

        /* Ensure all text in small boxes is white */
        .small-box h3, .small-box p, .small-box .small-box-footer {
            color: #ffffff !important;
        }

        /* Button Improvements */
        .btn {
            border-radius: 6px !important;
            font-weight: 500 !important;
        }

        /* Alert Improvements */
        .alert {
            border-radius: 8px !important;
            border: none !important;
        }
    </style>

    @yield('css')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                @auth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="far fa-user mr-1"></i>
                        <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <div class="dropdown-header bg-light">
                            <strong>{{ Auth::user()->name }}</strong><br>
                            <small class="text-muted">{{ Auth::user()->franchise->name ?? 'Franchise Owner' }}</small>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-user mr-2 text-primary"></i> Profile
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-cog mr-2 text-secondary"></i> Settings
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </li>
                @endauth
            </ul>
        </nav>

        <!-- Main Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ route('franchise.dashboard') }}" class="brand-link text-decoration-none">
                <i class="fas fa-building brand-image ml-2 mr-2" style="font-size: 1.8rem; color: #ffffff;"></i>
                <span class="brand-text font-weight-light text-white">Franchise Panel</span>
            </a>

            <div class="sidebar">
                @auth
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <div class="img-circle elevation-2" style="width: 34px; height: 34px; background: linear-gradient(45deg, #ffffff, #f8f9fa); display: flex; align-items: center; justify-content: center; color: #28a745; font-weight: bold; border-radius: 50%; font-size: 16px;">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </div>
                    <div class="info">
                        <a href="#" class="d-block text-white font-weight-medium">{{ Auth::user()->name }}</a>
                        <small class="text-light">{{ Auth::user()->franchise->name ?? 'Franchise Owner' }}</small>
                    </div>
                </div>
                @endauth

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="{{ route('franchise.dashboard') }}" class="nav-link {{ request()->routeIs('franchise.dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p class="ml-2">Dashboard</p>
                            </a>
                        </li>

                        <li class="nav-header">MY FRANCHISE</li>

                        <li class="nav-item">
                            <a href="{{ route('franchise.students.index') }}" class="nav-link {{ request()->routeIs('franchise.students.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p class="ml-2">My Students</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('franchise.courses.index') }}" class="nav-link {{ request()->routeIs('franchise.courses.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p class="ml-2">Available Courses</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('franchise.certificates.index') }}" class="nav-link {{ request()->routeIs('franchise.certificates.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-certificate"></i>
                                <p class="ml-2">Certificates</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('franchise.payments.index') }}" class="nav-link {{ request()->routeIs('franchise.payments.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-credit-card"></i>
                                <p class="ml-2">Payments</p>
                            </a>
                        </li>

                        <li class="nav-header">REPORTS</li>

                        <li class="nav-item">
                            <a href="{{ route('franchise.reports.index') }}" class="nav-link {{ request()->routeIs('franchise.reports.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p class="ml-2">Reports</p>
                            </a>
                        </li>

                        <li class="nav-header">SETTINGS</li>

                        <li class="nav-item">
                            <a href="{{ route('franchise.settings.index') }}" class="nav-link {{ request()->routeIs('franchise.settings.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cog"></i>
                                <p class="ml-2">Settings</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 font-weight-bold text-dark">@yield('page-title', 'Dashboard')</h1>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </section>
        </div>

        <!-- Footer -->
        <footer class="main-footer text-sm">
            <strong>Copyright &copy; 2025 <a href="#" class="text-success">EduManagement System</a>.</strong>
            All rights reserved - Franchise Panel.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 1.0.0 | <b>User:</b> {{ Auth::user()->name ?? 'Guest' }}
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    @yield('js')
</body>
</html>
