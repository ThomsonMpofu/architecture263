<style>
    /* 1. Typography & Colors */
    @font-face {
        font-family: 'HP Simplified';
        src: local('HP Simplified'), local('Segoe UI'), local('Arial');
    }

    :root {
        --acz-sidebar-bg: #ffffff;
        --acz-sidebar-text: #444444;
        --acz-sidebar-primary: #012970; /* Navy */
        --acz-sidebar-accent: #4154f1;  /* Vibrant Blue */
        --acz-sidebar-hover: #f6f9ff;
        --acz-border-light: #ebeef4;
    }

    .acz-sidebar {
        width: 300px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        background: var(--acz-sidebar-bg);
        border-right: 1px solid var(--acz-border-light);
        padding: 20px;
        transition: all 0.3s;
        z-index: 996;
        font-family: 'HP Simplified', sans-serif;
        overflow-y: auto;
    }

    /* 2. Brand Section - Increased Logo Size */
    .acz-sidebar-brand {
        display: flex;
        align-items: center;
        gap: 15px; /* Slightly more gap for larger logo */
        padding-bottom: 25px;
        margin-bottom: 20px;
        border-bottom: 1px solid var(--acz-border-light);
        text-decoration: none;
    }

    .acz-brand-logo-img {
        height: 65px; /* Increased from 45px */
        width: auto;
        object-fit: contain;
    }

    .acz-brand-text-wrap {
        display: flex;
        flex-direction: column;
        color: var(--acz-sidebar-primary);
        font-weight: 700;
        line-height: 1.15;
        font-size: 0.9rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    /* 3. Navigation Layout */
    .sidebar-nav {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .nav-item { margin-bottom: 5px; }

    .nav-heading {
        font-size: 11px;
        text-transform: uppercase;
        color: #899bbd;
        font-weight: 600;
        margin: 15px 0 5px 15px;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        font-size: 15px;
        font-weight: 600;
        color: var(--acz-sidebar-primary);
        background: transparent;
        border-radius: 6px;
        text-decoration: none;
        transition: 0.3s;
    }

    .nav-link i {
        font-size: 20px;
        margin-right: 12px;
        color: #899bbd;
        transition: 0.3s;
    }

    .nav-link:hover, .nav-link.active {
        background: var(--acz-sidebar-hover);
        color: var(--acz-sidebar-accent);
    }

    .nav-link:hover i, .nav-link.active i {
        color: var(--acz-sidebar-accent);
    }

    /* 4. Submenu Styling */
    .nav-content {
        list-style: none;
        padding: 5px 0 5px 38px; 
        margin: 0;
    }

    .nav-content a {
        display: flex;
        align-items: center;
        padding: 10px 10px;
        font-size: 14px;
        font-weight: 500;
        color: var(--acz-sidebar-text);
        text-decoration: none;
        transition: 0.3s;
    }

    .nav-content a i {
        font-size: 8px; /* Bullet dot */
        margin-right: 12px;
        color: #bbbbbb;
    }

    .nav-content a:hover, .nav-content a.active {
        color: var(--acz-sidebar-accent);
    }

    .nav-content a.active i {
        color: var(--acz-sidebar-accent);
    }

    /* Chevron movement */
    .acz-chevron { transition: transform 0.2s; font-size: 16px !important; }
    .nav-link:not(.collapsed) .acz-chevron { transform: rotate(180deg); }

    .logout-link:hover { color: #dc3545 !important; background: #fff5f5 !important; }
    .logout-link:hover i { color: #dc3545 !important; }
</style>

<aside id="sidebar" class="acz-sidebar">
    <a href="{{ route('dashboard.index') }}" class="acz-sidebar-brand">
        <img src="{{ asset('images/main_logo.png') }}" alt="ACZ" class="acz-brand-logo-img">
        <div class="acz-brand-text-wrap">
            <span>Institute of</span>
            <span>Architects of</span>
            <span>Zimbabwe</span>
        </div>
    </a>

    <nav>
        <ul class="sidebar-nav" id="sidebar-nav">

            {{-- Dashboard --}}
            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ route('dashboard.index') }}" data-url="{{ route('dashboard.index') }}">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            {{-- Professionals Registry --}}
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#prof-registry-nav" href="#">
                    <i class="ri-user-star-line"></i>
                    <span>Professionals Registry</span>
                    <i class="ri-arrow-down-s-line ms-auto acz-chevron"></i>
                </a>
                <ul id="prof-registry-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                    <li><a href="{{ url('/professionals-registry') }}"><i class="ri-checkbox-blank-circle-fill"></i><span>Overview</span></a></li>
                    <li><a href="{{ url('/professionals') }}"><i class="ri-checkbox-blank-circle-fill"></i><span>Registered Professionals</span></a></li>
                    <li><a href="{{ url('/firms') }}"><i class="ri-checkbox-blank-circle-fill"></i><span>Firms / Practices</span></a></li>
                    <li><a href="{{ url('/registry-applications') }}"><i class="ri-checkbox-blank-circle-fill"></i><span>Applications</span></a></li>
                    <li><a href="{{ url('/certificates-licenses') }}"><i class="ri-checkbox-blank-circle-fill"></i><span>Certificates & Licenses</span></a></li>
                </ul>
            </li>

            {{-- Communications & Notifications --}}
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#comm-nav" href="#">
                    <i class="ri-notification-3-line"></i>
                    <span>Communications</span>
                    <i class="ri-arrow-down-s-line ms-auto acz-chevron"></i>
                </a>
                <ul id="comm-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                    <li><a href="{{ url('/communications') }}"><i class="ri-checkbox-blank-circle-fill"></i><span>Communications</span></a></li>
                    <li><a href="{{ url('/notifications') }}"><i class="ri-checkbox-blank-circle-fill"></i><span>Notifications</span></a></li>
                    <li><a href="{{ url('/communication-templates') }}"><i class="ri-checkbox-blank-circle-fill"></i><span>Templates</span></a></li>
                    <li><a href="{{ url('/communication-logs') }}"><i class="ri-checkbox-blank-circle-fill"></i><span>Delivery Logs</span></a></li>
                </ul>
            </li>

            {{-- User Management --}}
            @canany(['user-list', 'role-list'])
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#user-mgmt-nav" href="#">
                    <i class="ri-team-line"></i>
                    <span>User Management</span>
                    <i class="ri-arrow-down-s-line ms-auto acz-chevron"></i>
                </a>
                <ul id="user-mgmt-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                    @can('user-list')
                    <li><a href="{{ route('users.index') }}"><i class="ri-checkbox-blank-circle-fill"></i><span>Users</span></a></li>
                    @endcan
                    @can('role-list')
                    <li><a href="{{ route('roles.index') }}"><i class="ri-checkbox-blank-circle-fill"></i><span>Roles & Permissions</span></a></li>
                    @endcan
                </ul>
            </li>
            @endcanany

            <li class="nav-heading">Settings</li>

            {{-- Portal Settings --}}
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#settings-nav" href="#">
                    <i class="ri-settings-3-line"></i>
                    <span>Portal Settings</span>
                    <i class="ri-arrow-down-s-line ms-auto acz-chevron"></i>
                </a>
                <ul id="settings-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                    <li><a href="#"><i class="ri-checkbox-blank-circle-fill"></i><span>My Profile</span></a></li>
                    <li><a href="#"><i class="ri-checkbox-blank-circle-fill"></i><span>General Settings</span></a></li>
                    <li><a href="{{ url('/portal-settings/branding') }}"><i class="ri-checkbox-blank-circle-fill"></i><span>Branding & Appearance</span></a></li>
                </ul>
            </li>

            {{-- Change Password --}}
            <li class="nav-item">
                <a class="nav-link collapsed" href="#">
                    <i class="ri-lock-password-line"></i>
                    <span>Change Password</span>
                </a>
            </li>

            {{-- Help & Manual --}}
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#help-nav" href="#">
                    <i class="ri-question-line"></i>
                    <span>Help & Manual</span>
                    <i class="ri-arrow-down-s-line ms-auto acz-chevron"></i>
                </a>
                <ul id="help-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                    <li><a href="#"><i class="ri-checkbox-blank-circle-fill"></i><span>User Manual</span></a></li>
                    <li><a href="{{ url('/help') }}"><i class="ri-checkbox-blank-circle-fill"></i><span>Support</span></a></li>
                </ul>
            </li>

            {{-- Logout --}}
            <li class="nav-item">
                <a class="nav-link collapsed logout-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="ri-logout-box-r-line"></i>
                    <span>Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </li>

        </ul>
    </nav>
</aside>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Handle Accordion Toggling
    $('.nav-link[data-bs-toggle="collapse"]').on('click', function(e) {
        e.preventDefault();
        
        var targetId = $(this).attr('data-bs-target');
        var $targetMenu = $(targetId);
        var $allMenus = $('.nav-content');
        var $allLinks = $('.nav-link[data-bs-toggle="collapse"]');

        // If clicking a menu that is already open, just close it
        if ($targetMenu.is(':visible')) {
            $targetMenu.slideUp(300);
            $(this).addClass('collapsed').attr('aria-expanded', 'false');
        } 
        // Otherwise, close all others and open this one
        else {
            $allMenus.slideUp(300);
            $allLinks.addClass('collapsed').attr('aria-expanded', 'false');
            
            $targetMenu.slideDown(300);
            $(this).removeClass('collapsed').attr('aria-expanded', 'true');
        }
    });

    // Auto-Highlight Active Page & Expand its Parent
    var currentUrl = window.location.href.split(/[?#]/)[0];
    $('.nav-content a, .nav-link').each(function() {
        if (this.href === currentUrl) {
            $(this).addClass('active');
            
            // If it's inside a sub-menu, expand that menu
            var $parentMenu = $(this).closest('.nav-content');
            if ($parentMenu.length) {
                $parentMenu.show();
                var triggerId = $parentMenu.attr('id');
                $('.nav-link[data-bs-target="#' + triggerId + '"]').removeClass('collapsed').attr('aria-expanded', 'true');
            }
        }
    });
});
</script>