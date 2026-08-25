<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($validated['username']).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'username' => [trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ])],
            ]);
        }

        $user = User::where('username', $validated['username'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'username' => [trans('auth.failed')],
            ]);
        }

        if ($user->is_suspended) {
            throw ValidationException::withMessages([
                'username' => ['This account has been suspended.'],
            ]);
        }

        if (! $user->email_verified_at) {
            throw ValidationException::withMessages([
                'username' => ['This account has not been activated yet.'],
            ]);
        }

        if ($user->hasRole('architect') && ! $user->approved_at) {
            throw ValidationException::withMessages([
                'username' => ['Your architect registration is pending admin approval.'],
            ]);
        }

        RateLimiter::clear($throttleKey);

        $token = $user->createToken($validated['device_name'] ?? 'api')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $this->transform($user),
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($this->transform($request->user()));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    private function transform(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'roles' => $user->getRoleNames(),
            'blue_book_requested_at' => optional($user->blue_book_requested_at)->format('Y-m-d'),
            'blue_book_active' => (bool) $user->subscription_active,
        ];
    }
}
