<?php
// config/adminlte.php - SIMPLIFIED WITHOUT PROBLEMATIC FILTERS
return [
    'title' => 'Edu Management',
    'title_prefix' => '',
    'title_postfix' => '',

    'logo' => '<b>Edu</b>Management',
    'logo_img' => null,
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Edu Management',

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-nav',
    'classes_topnav_container' => 'container',

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    'use_route_url' => false,
    'dashboard_url' => 'admin',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,

    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    // SIMPLE MENU WITHOUT PROBLEMATIC FILTERS
    'menu' => [
        // Dashboard
        [
            'text' => 'Dashboard',
            'route' => 'admin.dashboard',
            'icon' => 'fas fa-fw fa-tachometer-alt',
        ],

        // Management Section
        [
            'header' => 'MANAGEMENT',
        ],
        [
            'text' => 'Franchises',
            'route' => 'admin.franchises.index',
            'icon' => 'fas fa-fw fa-building',
        ],
        [
            'text' => 'Students',
            'url' => '#',
            'icon' => 'fas fa-fw fa-users',
        ],
        [
            'text' => 'Courses',
            'route' => 'admin.courses.index',
            'icon' => 'fas fa-fw fa-book',
        ],
        [
            'text' => 'Exams',
            'url' => '#',
            'icon' => 'fas fa-fw fa-clipboard-list',
        ],
        [
            'text' => 'Certificates',
            'route' => 'admin.certificates.index',
            'icon' => 'fas fa-fw fa-certificate',
        ],

        // Financial Section
        [
            'header' => 'FINANCIAL',
        ],
        [
            'text' => 'Payments',
            'url' => '#',
            'icon' => 'fas fa-fw fa-credit-card',
        ],
        [
            'text' => 'Reports',
            'url' => '#',
            'icon' => 'fas fa-fw fa-chart-bar',
        ],

        // Settings Section
        [
            'header' => 'SETTINGS',
        ],
        [
            'text' => 'Profile',
            'route' => 'profile.edit',
            'icon' => 'fas fa-fw fa-user-cog',
        ],
    ],

    // REMOVE ALL FILTERS TO AVOID ERRORS
    'filters' => [],

    // SIMPLIFIED PLUGINS
    'plugins' => [
        'Datatables' => [
            'active' => false,
        ],
    ],

    'livewire' => false,
];
