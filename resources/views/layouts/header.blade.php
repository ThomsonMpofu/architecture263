<!-- ======= Header ======= -->
@php
    use Illuminate\Support\Facades\Auth;

    $notifications = collect();
    $unreadCount = 0;
    // Use Auth user or a default object
    $userProfile = Auth::user(); 
@endphp

<style>
    /* ACZ/IAZ Header polish (aligned to login page theme) */
    #header.acz-header {
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        box-shadow: 0 8px 18px rgba(0,0,0,.03);
        height: 72px;
        padding: 0 14px;
        z-index: 997;
    }

    #header .logo {
        text-decoration: none;
        gap: 10px;
    }

    #header .brand-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    #header .brand-logo-wrap {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: transparent;
    }

    #header .brand-logo {
        width: 100%;
        height: 100%;
        object-fit: contain; /* Ensure logo fits nicely */
        /* Remove clip-path which might cut off parts */
        transform: scale(1.04);
        display: block;
    }

    #header .brand-text {
        line-height: 1.05;
        color: #1f2937;
        text-transform: uppercase;
        letter-spacing: .28px;
        font-size: .72rem;
        white-space: nowrap;
    }

    #header .brand-text div {
        margin: 0;
    }

    #header .toggle-sidebar-btn {
        font-size: 1.35rem;
        color: #374151;
        margin-left: 10px;
        cursor: pointer;
        transition: color .15s ease;
    }

    #header .toggle-sidebar-btn:hover {
        color: #0096d6;
    }

    #header .header-nav .nav-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #374151;
        transition: background-color .15s ease, color .15s ease;
        position: relative;
    }

    #header .header-nav .nav-icon:hover {
        background: #f3f6fb;
        color: #0096d6;
    }

    #header .header-nav .nav-icon i {
        font-size: 1.2rem;
        line-height: 1;
    }

    #header .badge-number {
        position: absolute;
        top: 4px;
        right: 2px;
        font-size: .65rem;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50px;
        background: #0096d6 !important;
        color: #fff;
        border: 2px solid #fff;
    }

    #header .nav-profile {
        border-radius: 12px;
        padding: 4px 8px 4px 4px !important;
        transition: background-color .15s ease;
    }

    #header .nav-profile:hover {
        background: #f3f6fb;
    }

    #header .nav-profile img {
        width: 32px !important;
        height: 32px !important;
        object-fit: cover;
        border: 1px solid #e5e7eb;
    }

    #header .nav-profile .dropdown-toggle {
        color: #1f2937;
        font-weight: 600;
        font-size: .78rem;
        max-width: 180px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    #header .dropdown-menu {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 14px 30px rgba(0,0,0,.08);
        padding-top: .45rem;
        padding-bottom: .45rem;
    }

    #header .dropdown-menu .dropdown-divider {
        margin: .35rem 0;
        border-top-color: #eef2f7;
    }

    #header .dropdown-header {
        color: #374151;
    }

    #header .notifications .notification-item {
        padding: .55rem .85rem;
    }

    #header .notifications .notification-item a {
        text-decoration: none;
        color: inherit;
        flex: 1;
    }

    #header .notifications .notification-item h4 {
        font-size: .8rem;
        margin: 0 0 .15rem;
        color: #111827;
    }

    #header .notifications .notification-item p {
        margin: 0 0 .12rem;
        color: #6b7280;
        font-size: .72rem;
        line-height: 1.2;
    }

    #header .mark-as-read {
        border-radius: 8px;
        font-size: .72rem;
        white-space: nowrap;
    }

    #header .dropdown-item {
        font-size: .85rem;
        padding: .5rem .85rem;
        display: flex;
        align-items: center;
        gap: .55rem;
    }

    #header .dropdown-item i {
        font-size: 1rem;
        color: #6b7280;
    }

    #header .dropdown-item:hover {
        background: #f7faff;
        color: #0096d6;
    }

    #header .dropdown-item:hover i {
        color: #0096d6;
    }

    @media (max-width: 991px) {
        #header .brand-text {
            display: none;
        }
        #header .nav-profile .dropdown-toggle {
            max-width: 110px;
        }
    }

    @media (max-width: 575px) {
        #header.acz-header {
            padding: 0 10px;
        }
        #header .brand-logo-wrap {
            width: 40px;
            height: 40px;
        }
    }
</style>

<header id="header" class="header acz-header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
        <a href="{{ url('home') }}" class="logo d-flex align-items-center">
            <div class="brand-wrap">
                <div class="brand-logo-wrap">
                    <img src="{{ asset('images/main_logo.png') }}"
                         alt="IAZ Logo"
                         class="brand-logo"
                         onerror="this.src='https://via.placeholder.com/44?text=IAZ'">
                </div>

                <div class="brand-text d-none d-md-block">
                    <div>Institute of</div>
                    <div>Architects of</div>
                    <div>Zimbabwe</div>
                </div>
            </div>
        </a>

        {{-- Sidebar toggle --}}
        <i class="ri-menu-line toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center mb-0">

            {{-- Notifications --}}
            <li class="nav-item dropdown">
                <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown" aria-label="Notifications">
                    <i class="ri-notification-3-line"></i>

                    @if($unreadCount > 0)
                        <span id="unread-count" class="badge badge-number">{{ $unreadCount }}</span>
                    @endif
                </a>

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications" style="width: 25rem;">
                    <li class="dropdown-header d-flex justify-content-between align-items-center">
                        <span id="notification-text">
                            @if($unreadCount > 0)
                                You have {{ $unreadCount }} new notification{{ $unreadCount > 1 ? 's' : '' }}
                            @else
                                You have no new notifications
                            @endif
                        </span>
                    </li>

                    <li><hr class="dropdown-divider"></li>

                    @if ($notifications->isEmpty())
                        <li class="text-center text-muted py-3 small">No new notifications</li>
                    @else
                        @foreach ($notifications as $notification)
                            @php
                                $data = json_decode($notification->data, true) ?? [];
                            @endphp

                            <li class="notification-item d-flex align-items-start gap-2">
                                <a href="{{ $data['url'] ?? '#' }}" class="d-flex align-items-start">
                                    <div>
                                        <h4>{{ $data['title'] ?? 'Notification' }}</h4>
                                        <p>{{ $data['message'] ?? 'You have a new update.' }}</p>
                                        <p class="text-muted">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</p>
                                    </div>
                                </a>

                                <button type="button"
                                        class="btn btn-sm btn-outline-secondary mark-as-read"
                                        data-id="{{ $notification->id }}">
                                    Mark as read
                                </button>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                        @endforeach
                    @endif
                </ul>
            </li><!-- End Notifications -->

            {{-- Messages (optional placeholder, can remove if not used) --}}
            <li class="nav-item dropdown">
                <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown" aria-label="Messages">
                    <i class="ri-message-3-line"></i>
                </a>
            </li><!-- End Messages Nav -->

            {{-- Profile --}}
            <li class="nav-item dropdown pe-2 pe-md-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->username ?? 'User') }}&background=0D8ABC&color=fff" alt="Profile" class="rounded-circle">

                    @auth
                        <span class="d-none d-md-block dropdown-toggle ps-2">
                            {{ $userProfile->name ?? (Auth::user()->username ?? 'User') }}
                        </span>
                    @else
                        <script>window.location.href = '{{ route('login') }}';</script>
                    @endauth
                </a><!-- End Profile Image Icon -->

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        @auth
                            <h6 class="fw-semibold mb-1" style="font-size:.85rem;">
                                {{ $userProfile->name ?? (Auth::user()->username ?? 'User') }}
                            </h6>
                            <span class="text-muted" style="font-size:.75rem;">
                                {{ $userProfile->job_title ?? 'Member' }}
                            </span>
                        @else
                            <script>window.location.href = '{{ route('login') }}';</script>
                        @endauth
                    </li>

                    <li><hr class="dropdown-divider"></li>

                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="ri-user-3-line"></i>
                            <span>My Profile</span>
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="ri-lock-password-line"></i>
                            <span>Change Password</span>
                        </a>
                    </li>

                    <li><hr class="dropdown-divider"></li>

                    <li>
                        <a class="dropdown-item" href="#"
                           onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                            <i class="ri-logout-box-r-line"></i>
                            <span>Logout</span>
                        </a>
                        <form id="logout-form-header" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul><!-- End Profile Dropdown Items -->
            </li><!-- End Profile Nav -->
        </ul>
    </nav><!-- End Icons Navigation -->
</header><!-- End Header -->

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.body.addEventListener("click", function (event) {
        const markBtn = event.target.closest(".mark-as-read");
        if (!markBtn) return;

        event.preventDefault();

        const notificationId = markBtn.getAttribute("data-id");
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        if (!notificationId || !csrfToken) return;

        fetch(`/notifications/${notificationId}/read`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error("Failed to mark as read");
                return;
            }

            // Remove the notification row + nearest divider after it (if present)
            const notificationItem = markBtn.closest('.notification-item');
            if (notificationItem) {
                const nextDivider = notificationItem.nextElementSibling;
                notificationItem.remove();

                if (nextDivider && nextDivider.querySelector('.dropdown-divider')) {
                    nextDivider.remove();
                }
            }

            const unreadBadge = document.getElementById("unread-count");
            const notificationText = document.getElementById("notification-text");
            const notificationsMenu = document.querySelector(".dropdown-menu.notifications");

            let currentUnreadCount = unreadBadge ? parseInt(unreadBadge.innerText || '0', 10) : 0;
            let newUnreadCount = Math.max(0, currentUnreadCount - 1);

            if (unreadBadge) {
                if (newUnreadCount > 0) {
                    unreadBadge.innerText = newUnreadCount;
                } else {
                    unreadBadge.classList.add("d-none");
                }
            }

            if (notificationText) {
                notificationText.innerText = newUnreadCount > 0
                    ? `You have ${newUnreadCount} new notification${newUnreadCount > 1 ? 's' : ''}`
                    : "You have no new notifications";
            }

            // If no items left, show empty state
            const remainingItems = notificationsMenu?.querySelectorAll('.notification-item').length || 0;
            if (remainingItems === 0 && notificationsMenu) {
                const hasEmptyState = notificationsMenu.querySelector('.notifications-empty-state');
                if (!hasEmptyState) {
                    const emptyState = document.createElement('li');
                    emptyState.className = 'text-center text-muted py-3 small notifications-empty-state';
                    emptyState.textContent = 'No new notifications';
                    notificationsMenu.appendChild(emptyState);
                }
            }
        })
        .catch(error => console.error("Error:", error));
    });
});
</script>