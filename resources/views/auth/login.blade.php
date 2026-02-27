{{-- resources/views/auth/login.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ACZ | Sign In</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Remix Icons (line icons) --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        /* HP Simplified Font (self-hosted) */
        @font-face {
            font-family: "HP Simplified";
            src: url("{{ asset('fonts/hp/HP-Simplified.woff2') }}") format("woff2"),
                 url("{{ asset('fonts/hp/HP-Simplified.woff') }}") format("woff");
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: "HP Simplified";
            src: url("{{ asset('fonts/hp/HP-Simplified-Bold.woff2') }}") format("woff2"),
                 url("{{ asset('fonts/hp/HP-Simplified-Bold.woff') }}") format("woff");
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }

        :root{
            --brand-blue: #0096d6;
            --brand-blue-dark: #0077b6;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --bg: #f5f7fb;
        }

        body {
            font-family: "HP Simplified", "Segoe UI", Arial, sans-serif !important;
            background: var(--bg);
            color: var(--text-main);
        }

        .login-wrap {
            min-height: 100vh;
        }

       .brand-top {
    display: flex;
    align-items: center;
    gap: 12px;
}

.brand-top-text {
    font-family: "HP Simplified", "Segoe UI", Arial, sans-serif;
    font-weight: 400;
    font-size: 0.9rem;
    line-height: 1.15;
    letter-spacing: 0.4px;
    color: #1f2937;
    text-transform: uppercase;
    text-align: left;
}

/* Logo wrapper (removes visible white outliers by circular crop) */
.page-logo-wrap {
    width: 100px;
    height: 100px;
    margin: 0 auto;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
}

.page-logo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    clip-path: circle(48% at 50% 50%);
    transform: scale(1.04);
    display: block;
}

/* Optional responsiveness: stack on small screens */
@media (max-width: 480px) {
    .brand-top {
        gap: 10px;
    }

    .brand-top-text {
        font-size: 0.82rem;
        line-height: 1.1;
    }

    .page-logo-wrap {
        width: 86px;
        height: 86px;
    }
}

        .login-card {
            max-width: 500px;
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0,0,0,.06);
            background: #fff;
            overflow: hidden;
        }

       

        .signin-text {
            color: var(--text-muted);
            font-size: .92rem;
            margin-bottom: 1rem;
        }


  .signin-title {
    font-weight: 700;
    font-size: 1.3rem;
    margin-top: .6rem;
    margin-bottom: .25rem;
}

        .form-label {
            font-weight: 600;
            font-size: .9rem;
            color: #374151;
        }

        .form-control {
            border-radius: 10px;
            padding: .78rem .95rem;
            border: 1px solid var(--border);
        }

        .form-control:focus {
            border-color: rgba(0,150,214,.55);
            box-shadow: 0 0 0 3px rgba(0,150,214,.10);
        }

        .password-wrap {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            color: #9ca3af;
            cursor: pointer;
            font-size: 1.1rem;
        }

        .btn-primary-custom {
            background: var(--brand-blue);
            border: none;
            border-radius: 10px;
            padding: .8rem 1rem;
            font-weight: 700;
            color: #fff;
        }

        .btn-primary-custom:hover {
            background: var(--brand-blue-dark);
            color: #fff;
        }

        .btn-oauth {
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fff;
            color: #374151;
            font-weight: 600;
            padding: .75rem .9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            transition: all .15s ease-in-out;
        }

        .btn-oauth:hover {
            background: #f9fafb;
            color: #111827;
            border-color: #d8dee8;
        }

        .btn-oauth i {
            font-size: 1.15rem;
            line-height: 1;
        }

        .btn-google {
            background: #fff;
        }

        .btn-google i {
            color: #ea4335;
        }

        .btn-facebook {
            background: #f7fbff;
            border-color: #dbeafe;
        }

        .btn-facebook i {
            color: #1877f2;
        }

        .btn-linkedin {
            background: #f8fbff;
            border-color: #dbeafe;
        }

        .btn-linkedin i {
            color: #0a66c2;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: .8rem;
            margin: 1rem 0;
            color: #9ca3af;
            font-size: .74rem;
            text-transform: uppercase;
            letter-spacing: .1em;
        }

        .divider::before, .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #edf2f7;
        }

        .small-link {
            color: var(--brand-blue);
            text-decoration: none;
            font-weight: 600;
        }

        .small-link:hover {
            color: var(--brand-blue-dark);
            text-decoration: underline;
        }

        .alert {
            border-radius: 10px;
            font-size: .9rem;
        }
    </style>
</head>
<body class="d-flex align-items-center">
<div class="container login-wrap d-flex flex-column align-items-center justify-content-center py-5">

    {{-- Logo outside the card --}}
    <div class="mb-3 text-center">
       {{-- Logo outside the card --}}
{{-- Logo outside the card --}}
<div class="mb-3 d-flex justify-content-center">
    <div class="brand-top">
        <div class="page-logo-wrap">
            <img src="{{ asset('images/main_logo.png') }}"
                 alt="ACZ Logo"
                 class="page-logo"
                 onerror="this.src='https://via.placeholder.com/96?text=ACZ'">
        </div>

        <div class="brand-top-text">
            <div>INSTITUTE OF</div>
            <div>ARCHITECTS OF</div>
            <div>ZIMBABWE</div>
        </div>
    </div>
</div>

             


             
    </div>

    <div class="login-card">
        <div class="p-4 p-md-4">

            {{-- Messages --}}
            @if (session('status'))
                <div class="alert alert-success py-2 mb-3">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger py-2 mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <h1 class="signin-title">Sign in</h1>
            <p class="signin-text">Enter your credentials to access the portal.</p>

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-control @error('username') is-invalid @enderror"
                        placeholder="Enter your username"
                        value="{{ old('username') }}"
                        required
                        autofocus
                    >
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-wrap">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="••••••••"
                            required
                        >
                        <i class="ri-eye-line toggle-password" id="togglePasswordIcon"></i>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label small text-muted" for="remember">
                            Remember session
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="small-link small">Forgot password?</a>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary-custom w-100 mb-3" id="submitBtn">
                    Sign in to Portal
                </button>

         

                {{-- Google OAuth --}}

       <a href="{{ route('oauth.redirect', 'google') }}" class="btn-oauth btn-google w-100 mb-2 mt-2">
                <i class="ri-google-line"></i>
                    Sign in with Google
                </a>

                {{-- Facebook OAuth --}}
                <a href="{{ route('oauth.redirect', 'facebook') }}" class="btn-oauth btn-facebook w-100 mb-2">
                    <i class="ri-facebook-circle-line"></i>
                    Sign in with Facebook
                </a>

                {{-- LinkedIn OAuth --}}
                <a href="{{ route('oauth.redirect', 'linkedin') }}" class="btn-oauth btn-linkedin w-100">
                    <i class="ri-linkedin-box-line"></i>
                    Sign in with LinkedIn
                </a>
            </form>

            @if (Route::has('register'))
                <p class="text-center mt-4 mb-0 small text-muted">
                    New to the council?
                    <a href="{{ route('register') }}" class="small-link">Create an account</a>
                </p>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('togglePasswordIcon');
    const loginForm = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');

    if (toggleIcon) {
        toggleIcon.addEventListener('click', function () {
            const hidden = passwordInput.type === 'password';
            passwordInput.type = hidden ? 'text' : 'password';
            toggleIcon.className = hidden ? 'ri-eye-off-line toggle-password' : 'ri-eye-line toggle-password';
        });
    }

    if (loginForm) {
        loginForm.addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Authenticating...';
        });
    }
</script>
</body>
</html>