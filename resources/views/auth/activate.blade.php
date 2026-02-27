@extends('layouts.auth')

@section('content')
<div class="card">
    <div class="card-header">{{ __('Activate Account') }}</div>

    <div class="card-body">
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
@endsection