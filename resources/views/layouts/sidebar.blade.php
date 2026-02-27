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
        z-index: 999; /* Ensure sidebar is on top of header for visibility */
        font-family: 'HP Simplified', sans-serif;
        overflow-y: auto;
        scrollbar-width: thin; /* Firefox */
        scrollbar-color: #c1c1c1 #f1f1f1; /* Firefox */
    }

    /* Mobile Sidebar State (Hidden by default) */
    @media (max-width: 1199px) {
        .acz-sidebar {
            left: -300px;
        }
    }

    /* Toggled State:
       - On Desktop: Hide sidebar
       - On Mobile: Show sidebar
    */
    .toggle-sidebar .acz-sidebar {
        left: -300px;
    }

    @media (max-width: 1199px) {
        .toggle-sidebar .acz-sidebar {
            left: 0;
        }
    }

    /* Custom Scrollbar for Sidebar */
    .acz-sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .acz-sidebar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .acz-sidebar::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .acz-sidebar::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
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
        font-size: 15px;
        font-weight: 600;
        color: #4154f1; /* Changed to match image blue */
        transition: 0.3s;
        background: #f6f9ff;
        padding: 10px 15px;
        border-radius: 4px;
        text-decoration: none;
    }

    .nav-link i {
        font-size: 16px;
        margin-right: 10px;
        color: #4154f1;
    }

    .nav-link.collapsed {
        color: #012970;
        background: #fff;
    }

    .nav-link.collapsed i {
        color: #899bbd;
    }

    .nav-link:hover {
        color: #4154f1;
        background: #f6f9ff;
    }

    .nav-link:hover i {
        color: #4154f1;
    }

    .nav-link .bi-chevron-down {
        margin-right: 0;
        transition: transform 0.2s ease-in-out;
    }

    .nav-link:not(.collapsed) .bi-chevron-down {
        transform: rotate(180deg);
    }

    .nav-content {
        padding: 5px 0 0 0;
        margin: 0;
        list-style: none;
    }

    .nav-content a {
        display: flex;
        align-items: center;
        font-size: 14px;
        font-weight: 600;
        color: #012970 !important; /* Force visibility */
        transition: 0.3s;
        padding: 10px 0 10px 40px;
        text-decoration: none;
    }

    .nav-content a i {
        font-size: 6px;
        margin-right: 8px;
        line-height: 0;
        border-radius: 50%;
    }

    .nav-content a:hover,
    .nav-content a.active {
        color: #4154f1 !important; /* Force visibility on hover/active */
    }

    .nav-content a.active i {
        background-color: #4154f1;
    }

    /* 4. Submenu Visibility Fixes */
    /* Ensure submenu is visible and on top */
    .nav-content {
        position: relative !important;
        z-index: 9999 !important;
        background: transparent !important;
    }

    /* Submenu items container */
    .nav-content.collapse.show {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        z-index: 10000 !important;
        position: relative !important;
        background: transparent !important;
    }

    /* Individual submenu links */
    .nav-content a {
        position: relative !important;
        z-index: 10001 !important;
        background: #fff !important; /* Or your desired background */
    }

    /* Ensure sidebar itself is on top */
    .acz-sidebar {
        z-index: 999 !important;
        /* Fix for sidebar scroll - keep scroll but allow overflow */
        overflow-y: auto !important;
        overflow-x: hidden !important; /* Changed back to hidden to fix animation */
    }
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
                    <li><a href="{{ url('/professionals-registry') }}"><i class="ri-checkbox-blank-circle-line"></i><span>Overview</span></a></li>
                    <li><a href="{{ url('/professionals') }}"><i class="ri-checkbox-blank-circle-line"></i><span>Registered Professionals</span></a></li>
                    <li><a href="{{ url('/firms') }}"><i class="ri-checkbox-blank-circle-line"></i><span>Firms / Practices</span></a></li>
                    <li><a href="{{ url('/registry-applications') }}"><i class="ri-checkbox-blank-circle-line"></i><span>Applications</span></a></li>
                    <li><a href="{{ url('/certificates-licenses') }}"><i class="ri-checkbox-blank-circle-line"></i><span>Certificates & Licenses</span></a></li>
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
                    <li><a href="{{ url('/communications') }}"><i class="ri-checkbox-blank-circle-line"></i><span>Communications</span></a></li>
                    <li><a href="{{ url('/notifications') }}"><i class="ri-checkbox-blank-circle-line"></i><span>Notifications</span></a></li>
                    <li><a href="{{ url('/communication-templates') }}"><i class="ri-checkbox-blank-circle-line"></i><span>Templates</span></a></li>
                    <li><a href="{{ url('/communication-logs') }}"><i class="ri-checkbox-blank-circle-line"></i><span>Delivery Logs</span></a></li>
                </ul>
            </li>

            {{-- User Management --}}
            {{-- Removed @can checks for DB Facade compatibility --}}
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#user-mgmt-nav" href="#">
                    <i class="ri-team-line"></i>
                    <span>User Management</span>
                    <i class="ri-arrow-down-s-line ms-auto acz-chevron"></i>
                </a>
                <ul id="user-mgmt-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                    <li><a href="{{ route('users.invite.create') }}"><i class="ri-checkbox-blank-circle-line"></i><span>Invite User</span></a></li>
                    <li><a href="#"><i class="ri-checkbox-blank-circle-line"></i><span>Users</span></a></li>
                    <li><a href="#"><i class="ri-checkbox-blank-circle-line"></i><span>Roles & Permissions</span></a></li>
                </ul>
            </li>

            {{-- Uploads --}}
            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ route('documents.index') }}" data-url="{{ route('documents.index') }}">
                    <i class="ri-dashboard-line"></i>
                    <span>Document Upload</span>
                </a>
            </li>

            <li class="nav-heading">Settings</li>

            {{-- Portal Settings --}}
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#settings-nav" href="#">
                    <i class="ri-settings-3-line"></i>
                    <span>Portal Settings</span>
                    <i class="ri-arrow-down-s-line ms-auto acz-chevron"></i>
                </a>
                <ul id="settings-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                    <li><a href="#"><i class="ri-checkbox-blank-circle-line"></i><span>My Profile</span></a></li>
                    <li><a href="#"><i class="ri-checkbox-blank-circle-line"></i><span>General Settings</span></a></li>
                    <li><a href="{{ url('/portal-settings/branding') }}"><i class="ri-checkbox-blank-circle-line"></i><span>Branding & Appearance</span></a></li>
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
                    <li><a href="#"><i class="ri-checkbox-blank-circle-line"></i><span>User Manual</span></a></li>
                    <li><a href="{{ url('/help') }}"><i class="ri-checkbox-blank-circle-line"></i><span>Support</span></a></li>
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

<script>
$(document).ready(function() {
    // Auto-Highlight Active Page & Expand its Parent
    var currentUrl = window.location.href.split(/[?#]/)[0];
    $('.nav-content a, .nav-link').each(function() {
        if (this.href === currentUrl) {
            $(this).addClass('active');
            
            // If it's inside a sub-menu, expand that menu
            var $parentMenu = $(this).closest('.nav-content');
            if ($parentMenu.length) {
                $parentMenu.addClass('show');
                $parentMenu.prev('.nav-link').removeClass('collapsed').attr('aria-expanded', 'true');
            }
        }
    });
});
</script>