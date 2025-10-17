{{-- resources/views/layouts/navigation.blade.php - FIXED VERSION --}}
<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            {{ config('app.name', 'Laravel') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">

            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <!-- Authentication Links -->
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @endif

                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name ?? 'User' }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            @auth
                                @if(Auth::user()->hasRole('super_admin'))
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>{{ __('Dashboard') }}
                                    </a>
                                @elseif(Auth::user()->hasRole('franchise'))
                                    <a class="dropdown-item" href="{{ route('franchise.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>{{ __('Dashboard') }}
                                    </a>
                                @endif
                            @endauth

                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i>{{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
{{-- resources/views/layouts/navigation.blade.php - MODERN SAFE VERSION --}}
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-0" style="padding: 1rem 0; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important;">
    <div class="container">
        <!-- Brand Logo -->
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}" style="text-decoration: none;">
            <div class="brand-icon me-3" style="width: 45px; height: 45px; background: linear-gradient(45deg, #667eea, #764ba2); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                <i class="fas fa-graduation-cap" style="color: white; font-size: 20px;"></i>
            </div>
            <div>
                <span style="font-size: 1.5rem; font-weight: 700; background: linear-gradient(45deg, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    {{ config('app.name', 'EduManagement') }}
                </span>
                <div style="font-size: 0.75rem; color: #6c757d; font-weight: 500; margin-top: -2px;">
                    Education Management System
                </div>
            </div>
        </a>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler border-0 shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}" style="padding: 8px 12px; background: linear-gradient(45deg, #667eea, #764ba2); color: white;">
            <i class="fas fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Navigation (for public pages) -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link fw-medium" href="{{ url('/') }}" style="color: #495057; transition: color 0.3s ease;" onmouseover="this.style.color='#667eea'" onmouseout="this.style.color='#495057'">
                        <i class="fas fa-home me-1"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-medium" href="#" style="color: #495057; transition: color 0.3s ease;" onmouseover="this.style.color='#667eea'" onmouseout="this.style.color='#495057'">
                        <i class="fas fa-info-circle me-1"></i>About
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-medium" href="#" style="color: #495057; transition: color 0.3s ease;" onmouseover="this.style.color='#667eea'" onmouseout="this.style.color='#495057'">
                        <i class="fas fa-book me-1"></i>Courses
                    </a>
                </li>
            </ul>

            <!-- Right Side Navigation -->
            <ul class="navbar-nav ms-auto align-items-lg-center">
                @guest
                    <!-- Guest Users (Not Logged In) -->
                    @if (Route::has('login'))
                        <li class="nav-item me-2">
                            <a class="nav-link btn btn-outline-primary px-3 py-2" href="{{ route('login') }}" style="border-radius: 8px; font-weight: 500; transition: all 0.3s ease; border-color: #667eea; color: #667eea;" onmouseover="this.style.background='#667eea'; this.style.color='white'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='transparent'; this.style.color='#667eea'; this.style.transform='translateY(0)'">
                                <i class="fas fa-sign-in-alt me-1"></i>{{ __('Login') }}
                            </a>
                        </li>
                    @endif

                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary px-3 py-2" href="{{ route('register') }}" style="background: linear-gradient(45deg, #667eea, #764ba2); border: none; border-radius: 8px; color: white; font-weight: 500; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                <i class="fas fa-user-plus me-1"></i>{{ __('Register') }}
                            </a>
                        </li>
                    @endif
                @else
                    <!-- Authenticated Users -->
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center px-3 py-2" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: rgba(102, 126, 234, 0.1); border-radius: 10px; color: #495057; font-weight: 500; transition: all 0.3s ease; text-decoration: none;" onmouseover="this.style.background='rgba(102, 126, 234, 0.15)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='rgba(102, 126, 234, 0.1)'; this.style.transform='translateY(0)'">
                            <!-- User Avatar -->
                            <div class="user-avatar me-2" style="width: 35px; height: 35px; background: linear-gradient(45deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px;">
                                {{ Auth::check() ? substr(Auth::user()->name, 0, 1) : 'U' }}
                            </div>
                            <div class="user-info text-start">
                                <div style="font-size: 14px; line-height: 1.2;">{{ Auth::check() ? Auth::user()->name : 'User' }}</div>
                                <div style="font-size: 11px; opacity: 0.7; line-height: 1;">
                                    @auth
                                        {{ Auth::user()->roles->first()->name ?? 'User' }}
                                    @else
                                        Guest
                                    @endauth
                                </div>
                            </div>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" aria-labelledby="navbarDropdown" style="min-width: 250px; border-radius: 12px; background: white; box-shadow: 0 10px 40px rgba(0,0,0,0.1) !important;">
                            <!-- User Info Header -->
                            <div class="dropdown-header py-3 px-4" style="background: linear-gradient(45deg, #667eea, #764ba2); color: white; border-radius: 12px 12px 0 0; margin: -0.5rem -0.25rem 0.5rem -0.25rem;">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-3" style="width: 45px; height: 45px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                        {{ Auth::check() ? substr(Auth::user()->name, 0, 1) : 'U' }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 600;">{{ Auth::check() ? Auth::user()->name : 'User' }}</div>
                                        <div style="font-size: 13px; opacity: 0.9;">{{ Auth::check() ? Auth::user()->email : '' }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Dashboard Links -->
                            @auth
                                @if(Auth::user()->roles->first())
                                    @if(Auth::user()->hasRole('super_admin'))
                                        <a class="dropdown-item py-2 px-4" href="{{ route('admin.dashboard') }}" style="transition: all 0.3s ease;" onmouseover="this.style.background='#f8f9fa'; this.style.paddingLeft='20px'" onmouseout="this.style.background='transparent'; this.style.paddingLeft='1.5rem'">
                                            <i class="fas fa-tachometer-alt me-3 text-primary"></i>{{ __('Admin Dashboard') }}
                                        </a>
                                    @elseif(Auth::user()->hasRole('franchise'))
                                        <a class="dropdown-item py-2 px-4" href="{{ route('franchise.dashboard') }}" style="transition: all 0.3s ease;" onmouseover="this.style.background='#f8f9fa'; this.style.paddingLeft='20px'" onmouseout="this.style.background='transparent'; this.style.paddingLeft='1.5rem'">
                                            <i class="fas fa-tachometer-alt me-3 text-success"></i>{{ __('Franchise Dashboard') }}
                                        </a>
                                    @endif
                                @endif
                            @endauth

                            <!-- Profile Link -->
                            @auth
                                <a class="dropdown-item py-2 px-4" href="{{ route('profile.edit') }}" style="transition: all 0.3s ease;" onmouseover="this.style.background='#f8f9fa'; this.style.paddingLeft='20px'" onmouseout="this.style.background='transparent'; this.style.paddingLeft='1.5rem'">
                                    <i class="fas fa-user me-3 text-info"></i>{{ __('Profile Settings') }}
                                </a>
                            @endauth

                            <div class="dropdown-divider my-2"></div>

                            <!-- Logout -->
                            <a class="dropdown-item py-2 px-4 text-danger" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                               style="transition: all 0.3s ease;" onmouseover="this.style.background='#f8f9fa'; this.style.paddingLeft='20px'" onmouseout="this.style.background='transparent'; this.style.paddingLeft='1.5rem'">
                                <i class="fas fa-sign-out-alt me-3"></i>{{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<style>
/* Custom dropdown animations */
.dropdown-menu {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive adjustments */
@media (max-width: 991px) {
    .navbar-nav .nav-item {
        margin: 0.25rem 0;
    }

    .dropdown-menu {
        margin-top: 0.5rem !important;
        width: 100%;
    }

    .user-info {
        display: none;
    }

    .navbar-brand > div > span {
        font-size: 1.25rem !important;
    }
}

/* Smooth hover transitions */
.nav-link, .dropdown-item {
    transition: all 0.3s ease !important;
}

/* Brand hover effect */
.navbar-brand:hover .brand-icon {
    transform: rotate(5deg) scale(1.05);
    transition: transform 0.3s ease;
}
</style>
