@extends('layouts.auth')

@section('content')
<style>
    .activate-wrap {
        width: 100%;
        max-width: 520px;
        padding: 0 16px;
    }
    .brand-top {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
    }
    .page-logo-wrap {
        width: 82px;
        height: 82px;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0px 10px 24px rgba(1, 41, 112, 0.10);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 1px solid rgba(1, 41, 112, 0.08);
    }
    .page-logo {
        width: 58px;
        height: 58px;
        object-fit: contain;
    }
    .brand-top-text {
        text-align: left;
        color: #012970;
        font-weight: 700;
        letter-spacing: .5px;
        line-height: 1.15;
    }
    .brand-top-text div {
        font-size: 12px;
    }
    .activate-subtitle {
        text-align: center;
        color: #6c757d;
        margin-top: 10px;
        margin-bottom: 16px;
        font-size: 0.95rem;
    }
    .btn-primary {
        background-color: #012970;
        border-color: #012970;
    }
    .btn-primary:hover {
        background-color: #0d3d91;
        border-color: #0d3d91;
    }
    .card-header {
        padding-top: 18px;
        padding-bottom: 10px;
    }
</style>

<div class="activate-wrap">
    <div class="mb-3 d-flex justify-content-center">
        <div class="brand-top">
            <div class="page-logo-wrap">
                <img src="{{ asset('images/main_logo.png') }}" alt="ACZ Logo" class="page-logo" onerror="this.src='https://via.placeholder.com/96?text=ACZ'">
            </div>
            <div class="brand-top-text">
                <div>INSTITUTE OF</div>
                <div>ARCHITECTS OF</div>
                <div>ZIMBABWE</div>
            </div>
        </div>
    </div>

    <div class="activate-subtitle">
        Set a password to activate your account.
    </div>

    <div class="card">
        <div class="card-header">{{ __('Activate Account') }}</div>

        <div class="card-body pt-2">
            <div class="text-center text-muted mb-3" style="font-size:.9rem;">
                {{ $email }}
            </div>

            <form method="POST" action="{{ route('activate.store', ['token' => $token]) }}">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('Password') }}</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Activate Account') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
