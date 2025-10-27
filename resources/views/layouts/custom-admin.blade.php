{{-- resources/views/layouts/custom-admin.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Dashboard') - Edu Management System</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" />

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css" />

    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .main-sidebar {
            background: linear-gradient(180deg, #2f4158 0%, #1a2940 100%) !important;
        }
        .brand-link {
            background: linear-gradient(45deg, #007bff, #0056b3) !important;
            font-weight: 700 !important;
            font-size: 1.25rem;
            color: white !important;
            text-align: center;
        }
        .brand-link .brand-image {
            font-size: 2.5rem;
        }
        /* Sidebar links overrides */
        .nav-sidebar .nav-link {
            color: #bdd7ff !important;
            font-weight: 600;
            border-radius: 6px;
            margin: 0.1rem 0;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .nav-sidebar .nav-link:hover,
        .nav-sidebar .nav-link.active {
            background-color: #007bff !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.5) !important;
        }
        .nav-sidebar .nav-icon {
            color: #a5c7ff !important;
        }
        /* User panel */
        .user-panel .image .bg-info {
            background: linear-gradient(135deg, #007bff, #6610f2);
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.7);
            font-weight: 700;
            font-size: 1.02rem;
        }
        .user-panel .info > a {
            color: #dbe6ff !important;
            font-weight: 600;
        }
        /* Navbar */
        .main-header {
            background: #fff;
            border-bottom: 1px solid #dee2e6;
            box-shadow: 0 3px 6px rgba(0,0,0,0.05);
            padding: 0 1.5rem;
        }
        .nav-dashboard-link {
            color: #495057 !important;
            font-weight: 700;
        }
        .nav-dashboard-link:hover {
            color: #007bff !important;
        }
        .custom-badge {
            background-color: #007bff;
            font-weight: 600;
            font-size: 0.7rem;
            border-radius: 0.8rem;
            padding: 0.2rem 0.5rem;
            color: white;
        }
    </style>

    @yield('css')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
@php
    $user = Auth::user();
    $routePrefix = $user && $user->hasRole('super_admin') ? 'admin' : 'franchise';
    $isAdmin = $user && $user->hasRole('super_admin');
    $isFranchise = $user && $user->hasRole('franchise');
@endphp

<div class="wrapper">

    {{-- Top Navbar --}}
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        {{-- Left --}}
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route($routePrefix . '.dashboard') }}" class="nav-link nav-dashboard-link">
                    <i class="fas fa-home mr-1"></i>Dashboard
                </a>
            </li>
        </ul>

        {{-- Right --}}
        <ul class="navbar-nav ml-auto align-items-center">
            {{-- Notifications --}}
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-primary navbar-badge">3</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right shadow">
                    <span class="dropdown-item dropdown-header">3 Notifications</span>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-users text-success mr-2"></i> 5 new students enrolled
                        <span class="float-right text-muted text-sm">1 hr</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer text-center">View All Notifications</a>
                </div>
            </li>

            {{-- User Profile --}}
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-2" style="width:32px; height:32px">
                        {{ substr($user->name ?? 'U', 0, 1) }}
                    </div>
                    <span class="d-none d-md-inline">{{ $user->name ?? 'User' }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-right shadow" style="min-width: 240px;">
                    <li class="user-header bg-primary text-center p-3">
                        <div class="mb-2">
                            <i class="fas fa-user-circle fa-3x text-white"></i>
                        </div>
                        <p class="mb-0 font-weight-bold">{{ $user->name ?? 'User' }}</p>
                        <small class="text-white-50">{{ ucfirst($user->getRoleNames()->first() ?? 'Unknown') }}</small>
                    </li>
                    <li class="user-footer d-flex justify-content-between bg-light p-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-user-cog"></i> Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>

    {{-- Sidebar --}}
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="{{ route($routePrefix . '.dashboard') }}" class="brand-link text-center">
            <i class="fas fa-graduation-cap brand-image text-white"></i>
            <span class="brand-text font-weight-bold ml-1">EduManagement</span>
        </a>

        <div class="sidebar">
            <div class="user-panel mt-3 mb-3 d-flex align-items-center">
                <div class="image">
                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width:36px; height:36px; font-weight: 700; font-size: 1.1rem;">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                </div>
                <div class="info ml-2">
                    <a href="#" class="d-block text-white font-weight-semibold">{{ $user->name }}</a>
                    <small class="text-light">{{ ucfirst($user->getRoleNames()->first()) }}</small>
                </div>
            </div>

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

                    <li class="nav-item">
                        <a href="{{ route($routePrefix.'.dashboard') }}" class="nav-link {{ request()->routeIs($routePrefix.'.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-header text-light">MANAGEMENT</li>

                    @if($isAdmin)
                        <li class="nav-item">
                            <a href="{{ route('admin.franchises.index') }}" class="nav-link {{ request()->routeIs('admin.franchises.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-building"></i>
                                <p>Franchises</p>
                            </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a href="{{ route($routePrefix.'.students.index') }}" class="nav-link {{ request()->routeIs($routePrefix.'.students.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Students</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route($routePrefix.'.courses.index') }}" class="nav-link {{ request()->routeIs($routePrefix.'.courses.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-book"></i>
                            <p>Courses</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route($routePrefix.'.certificates.index') }}" class="nav-link {{ request()->routeIs($routePrefix.'.certificates.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-certificate"></i>
                            <p>Certificates</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route($routePrefix.'.payments.index') }}" class="nav-link {{ request()->routeIs($routePrefix.'.payments.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-credit-card"></i>
                            <p>Payments</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route($routePrefix.'.reports.index') }}" class="nav-link {{ request()->routeIs($routePrefix.'.reports.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Reports</p>
                        </a>
                    </li>

                    <li class="nav-header text-light">SETTINGS</li>

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

    {{-- Main content --}}
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <h1 class="m-0 font-weight-bold">@yield('page-title', 'Dashboard')</h1>
                <ol class="breadcrumb float-sm-right bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route($routePrefix.'.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">@yield('page-title', 'Dashboard')</li>
                </ol>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </section>
    </div>
</div>

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

@yield('js')
</body>
</html>
