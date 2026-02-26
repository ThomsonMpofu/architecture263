<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize incoming fields in case old login form still posts "email" or "username".
     */
    protected function prepareForValidation(): void
    {
        // If your form posts "username"
        if ($this->filled('username') && ! $this->filled('login')) {
            $this->merge(['login' => $this->input('username')]);
        }

        // Backwards compatibility: if old form posts "email"
        if ($this->filled('email') && ! $this->filled('login')) {
            $this->merge(['login' => $this->input('email')]);
        }

        // Always trim
        if ($this->filled('login')) {
            $this->merge(['login' => trim((string) $this->input('login'))]);
        }
    }

    public function rules(): array
    {
        return [
            // New: no email validation since you don’t use emails
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = (string) $this->input('login');
        $password = (string) $this->input('password');

        // ✅ Change this if your DB login column is different
        $loginColumn = 'username'; // e.g. 'ec_number' or 'user_number'

        $credentials = [
            $loginColumn => $login,
            'password' => $password,
        ];

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower((string) $this->input('login')) . '|' . $this->ip()
        );
    }
}