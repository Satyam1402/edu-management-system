{{-- resources/views/layouts/custom-admin.blade.php - NUCLEAR ZERO SPACING FIX --}}
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
        /* NUCLEAR OPTION - ELIMINATE ALL NAVBAR SPACING */
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0 !important;
            padding: 0 !important;
        }

        /* CUSTOM NAVBAR - ZERO SPACING GUARANTEED */
        .custom-navbar {
            position: fixed !important;
            top: 0 !important;
            right: 0 !important;
            left: 250px !important;
            height: 57px !important;
            background: #ffffff !important;
            border-bottom: 1px solid #dee2e6 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08) !important;
            z-index: 1030 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .custom-navbar-left {
            display: flex !important;
            align-items: center !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .custom-navbar-right {
            display: flex !important;
            align-items: center !important;
            padding-right: 1rem !important;
        }

        .hamburger-btn {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 57px !important;
            height: 57px !important;
            color: #495057 !important;
            text-decoration: none !important;
            margin: 0 !important;
            padding: 0 !important;
            background: none !important;
            border: none !important;
            transition: background-color 0.2s ease !important;
        }

        .hamburger-btn:hover {
            background: rgba(0,0,0,0.05) !important;
            color: #007bff !important;
            text-decoration: none !important;
        }

        .nav-dashboard-link {
            padding: 0 15px !important;
            color: #495057 !important;
            text-decoration: none !important;
            display: flex !important;
            align-items: center !important;
            height: 57px !important;
            transition: all 0.2s ease !important;
        }

        .nav-dashboard-link:hover {
            color: #007bff !important;
            text-decoration: none !important;
            background: rgba(0,123,255,0.05) !important;
        }

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
            padding-top: 57px !important;
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

        /* Enhanced User Avatar */
        .user-avatar {
            width: 28px;
            height: 28px;
            background: linear-gradient(45deg, #007bff, #6610f2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 12px;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
        }

        /* Enhanced Dropdown */
        .dropdown-menu {
            border-radius: 10px;
            border: none;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .dropdown-header {
            border-radius: 10px 10px 0 0;
            font-weight: 600;
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

        /* Navbar Button Styling */
        .navbar-btn {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 40px !important;
            height: 40px !important;
            color: #495057 !important;
            text-decoration: none !important;
            border-radius: 6px !important;
            margin-right: 8px !important;
            transition: all 0.2s ease !important;
        }

        .navbar-btn:hover {
            background: rgba(0,123,255,0.1) !important;
            color: #007bff !important;
            text-decoration: none !important;
        }

        .notification-badge {
            position: absolute !important;
            top: -3px !important;
            right: -3px !important;
            background: #ffc107 !important;
            color: white !important;
            border-radius: 50% !important;
            width: 18px !important;
            height: 18px !important;
            font-size: 10px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-weight: bold !important;
        }

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
            .custom-navbar {
                left: 0 !important;
            }
        }

        /* Card Hover Effects */
        .card {
            transition: all 0.3s ease;
            border-radius: 10px;
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

        /* Enhanced Breadcrumbs */
        .breadcrumb {
            background: transparent;
            padding: 0;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            color: #6c757d;
        }

        /* Enhanced Alerts */
        .alert {
            border-radius: 10px;
            border: none;
        }

        /* User Dropdown Styling */
        .user-dropdown-btn {
            display: flex !important;
            align-items: center !important;
            color: #495057 !important;
            text-decoration: none !important;
            padding: 8px 12px !important;
            border-radius: 6px !important;
            transition: all 0.2s ease !important;
        }

        .user-dropdown-btn:hover {
            background: rgba(0,123,255,0.05) !important;
            color: #007bff !important;
            text-decoration: none !important;
        }

        .user-name {
            font-weight: 500 !important;
            margin-left: 8px !important;
        }
    </style>

    @yield('css')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- CUSTOM NAVBAR - ZERO WHITE SPACE -->
        <div class="custom-navbar">
            <!-- Left Side - Hamburger Menu (ABSOLUTE ZERO SPACING) -->
            <div class="custom-navbar-left">
                <a href="#" class="hamburger-btn" data-widget="pushmenu">
                    <i class="fas fa-bars"></i>
                </a>
                <a href="{{ route('admin.dashboard') }}" class="nav-dashboard-link d-none d-sm-flex">
                    <i class="fas fa-home mr-2"></i>Dashboard
                </a>
            </div>

            <!-- Right Side - User Menu -->
            <div class="custom-navbar-right">
                <!-- Search -->
                <div class="dropdown">
                    <a href="#" class="navbar-btn" data-toggle="dropdown" title="Search">
                        <i class="fas fa-search"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right p-3" style="min-width: 300px;">
                        <form>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search franchises, students...">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="dropdown">
                    <a href="#" class="navbar-btn position-relative" data-toggle="dropdown" title="Notifications">
                        <i class="far fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" style="width: 320px;">
                        <div class="dropdown-header bg-primary text-white">
                            <i class="fas fa-bell mr-2"></i>3 Notifications
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <i class="fas fa-building text-primary"></i>
                                </div>
                                <div>
                                    <div class="font-weight-medium">New franchise registered</div>
                                    <div class="text-muted small">2 minutes ago</div>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <i class="fas fa-users text-success"></i>
                                </div>
                                <div>
                                    <div class="font-weight-medium">5 new students enrolled</div>
                                    <div class="text-muted small">1 hour ago</div>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item dropdown-footer bg-light text-center">
                            <i class="fas fa-eye mr-2"></i>View All Notifications
                        </a>
                    </div>
                </div>

                @auth
                <!-- User Dropdown -->
                <div class="dropdown">
                    <a href="#" class="user-dropdown-btn" data-toggle="dropdown">
                        <div class="user-avatar">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span class="user-name d-none d-md-inline">{{ Auth::user()->name }}</span>
                        <i class="fas fa-caret-down ml-2"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" style="min-width: 280px;">
                        <div class="dropdown-header bg-primary text-white">
                            <div class="d-flex align-items-center">
                                <div class="user-avatar mr-3" style="background: rgba(255,255,255,0.2);">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <div>
                                    <strong>{{ Auth::user()->name }}</strong><br>
                                    <small>Super Administrator</small>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">
                            <i class="fas fa-user mr-3 text-primary"></i> My Profile
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-cog mr-3 text-secondary"></i> Account Settings
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-question-circle mr-3 text-info"></i> Help & Support
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt mr-3"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <div>
                    <a class="btn btn-primary px-3" href="{{ route('login') }}">
                        <i class="fas fa-sign-in-alt mr-1"></i> Login
                    </a>
                </div>
                @endauth
            </div>
        </div>

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
                        <div class="img-circle elevation-2" style="width: 34px; height: 34px; background-color: #007bff; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; border-radius: 50%;">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                    </div>
                    <div class="info">
                        <a href="#" class="d-block text-white">{{ Auth::user()->name ?? 'User' }}</a>
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
                            <h1 class="m-0 font-weight-bold">@yield('page-title', 'Dashboard')</h1>
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
