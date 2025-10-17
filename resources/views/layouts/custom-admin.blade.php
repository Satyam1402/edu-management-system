{{-- resources/views/layouts/custom-admin.blade.php - SAFE VERSION --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Edu Management System</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <style>
        /* Custom Sidebar Styling */
        .main-sidebar {
            position: fixed !important;
            height: 100vh;
            background: linear-gradient(180deg, #343a40 0%, #495057 100%) !important;
        }

        .brand-link {
            background: linear-gradient(45deg, #007bff, #0056b3) !important;
            border-bottom: 3px solid rgba(255,255,255,0.2) !important;
        }

        .brand-link .brand-text {
            color: #ffffff !important;
            font-weight: 600 !important;
        }

        .content-wrapper {
            margin-left: 250px !important;
            min-height: 100vh;
        }

        .main-header {
            position: fixed !important;
            top: 0;
            right: 0;
            left: 250px;
            z-index: 1030;
        }

        .content-wrapper {
            padding-top: 57px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        }

        /* Sidebar Menu Styling */
        .nav-sidebar .nav-item > .nav-link {
            color: rgba(255,255,255,0.8) !important;
            border-radius: 5px !important;
            margin: 2px 8px !important;
            transition: all 0.3s ease !important;
        }

        .nav-sidebar .nav-item > .nav-link:hover {
            background-color: rgba(255,255,255,0.1) !important;
            color: #ffffff !important;
            transform: translateX(5px) !important;
        }

        .nav-sidebar .nav-item > .nav-link.active {
            background: linear-gradient(45deg, #28a745, #20c997) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3) !important;
        }

        /* Icon Colors */
        .nav-sidebar .fa-tachometer-alt { color: #ffc107 !important; }
        .nav-sidebar .fa-building { color: #17a2b8 !important; }
        .nav-sidebar .fa-users { color: #28a745 !important; }
        .nav-sidebar .fa-book { color: #007bff !important; }
        .nav-sidebar .fa-clipboard-list { color: #fd7e14 !important; }
        .nav-sidebar .fa-certificate { color: #dc3545 !important; }
        .nav-sidebar .fa-credit-card { color: #28a745 !important; }
        .nav-sidebar .fa-chart-bar { color: #17a2b8 !important; }
        .nav-sidebar .fa-user-cog { color: #6f42c1 !important; }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .main-sidebar {
                margin-left: -250px;
                transition: margin-left 0.3s ease;
            }
            .sidebar-open .main-sidebar {
                margin-left: 0;
            }
            .content-wrapper {
                margin-left: 0 !important;
            }
            .main-header {
                left: 0;
            }
        }

        /* Card Hover Effects */
        .card {
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .small-box {
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .small-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
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
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                @auth
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-user"></i> {{ Auth::user()->name ?? 'User' }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">
                            <i class="fas fa-user mr-2"></i> Profile
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </li>
                @else
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                </li>
                @endauth
            </ul>
        </nav>

        <!-- Main Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ route('admin.dashboard') }}" class="brand-link">
                <i class="fas fa-graduation-cap brand-image" style="font-size: 2rem; margin-left: 10px; margin-right: 10px;"></i>
                <span class="brand-text font-weight-light">EduManagement</span>
            </a>

            <div class="sidebar">
                @auth
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <div class="img-circle elevation-2" style="width: 34px; height: 34px; background-color: #007bff; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ Auth::user()->name ?? 'User' }}</a>
                        <small class="text-light">
                            {{ Auth::user()->roles->first()->name ?? 'User' }}
                        </small>
                    </div>
                </div>
                @endauth

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <li class="nav-header">MANAGEMENT</li>

                        <!-- Franchises -->
                        <li class="nav-item">
                            <a href="{{ route('admin.franchises.index') }}" class="nav-link {{ request()->routeIs('admin.franchises.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-building"></i>
                                <p>Franchises</p>
                            </a>
                        </li>

                        <!-- Students -->
                        <li class="nav-item">
                            <a href="{{ route('admin.students.index') }}" class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Students</p>
                            </a>
                        </li>

                        <!-- Courses -->
                        <li class="nav-item">
                            <a href="{{ route('admin.courses.index') }}" class="nav-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>Courses</p>
                            </a>
                        </li>

                        <!-- Exams -->
                        <li class="nav-item">
                            <a href="{{ route('admin.exams.index') }}" class="nav-link {{ request()->routeIs('admin.exams.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>Exams</p>
                            </a>
                        </li>

                        <!-- Certificates -->
                        <li class="nav-item">
                            <a href="{{ route('admin.certificates.index') }}" class="nav-link {{ request()->routeIs('admin.certificates.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-certificate"></i>
                                <p>Certificates</p>
                            </a>
                        </li>

                        <!-- Payments -->
                        <li class="nav-item">
                            <a href="{{ route('admin.payments.index') }}" class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-credit-card"></i>
                                <p>Payments</p>
                            </a>
                        </li>

                        <!-- Reports -->
                        <li class="nav-item">
                            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>Reports</p>
                            </a>
                        </li>

                        <li class="nav-header">SETTINGS</li>

                        <!-- Profile -->
                        <li class="nav-item">
                            <a href="{{ route('profile.edit') }}" class="nav-link">
                                <i class="nav-icon fas fa-user-cog"></i>
                                <p>Profile</p>
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
                            <h1 class="m-0">@yield('page-title', 'Dashboard')</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    @yield('page-title', 'Dashboard')
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <!-- Success Messages -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    <!-- Error Messages -->
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <h6><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </section>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    @yield('js')
</body>
</html>
