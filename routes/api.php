<?php

use App\Http\Controllers\PlanApplicationController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/auth/me', function (Request $request) {
    $authHeader = (string) $request->header('Authorization', '');
    if (! Str::startsWith($authHeader, 'Bearer ')) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    $rawToken = trim(Str::after($authHeader, 'Bearer '));
    if ($rawToken === '' || ! str_contains($rawToken, '|')) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    [$tokenId, $plainTextToken] = explode('|', $rawToken, 2);
    if (! ctype_digit($tokenId) || $plainTextToken === '') {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    $tokenRow = DB::table('personal_access_tokens')
        ->where('id', (int) $tokenId)
        ->where('tokenable_type', User::class)
        ->first();

    if (! $tokenRow) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    $hash = hash('sha256', $plainTextToken);
    if (! hash_equals((string) $tokenRow->token, $hash)) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    if ($tokenRow->expires_at !== null && now()->greaterThan($tokenRow->expires_at)) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    $user = DB::table('users')
        ->select(['id', 'name', 'username', 'email', 'email_verified_at', 'is_suspended'])
        ->where('id', $tokenRow->tokenable_id)
        ->first();

    if (! $user) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    if ((int) $user->is_suspended === 1 || $user->email_verified_at === null) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }

    DB::table('personal_access_tokens')->where('id', (int) $tokenId)->update([
        'last_used_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
        ],
    ]);
});

Route::post('/auth/logout', function (Request $request) {
    $authHeader = (string) $request->header('Authorization', '');
    if (! Str::startsWith($authHeader, 'Bearer ')) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    $rawToken = trim(Str::after($authHeader, 'Bearer '));
    if ($rawToken === '' || ! str_contains($rawToken, '|')) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    [$tokenId, $plainTextToken] = explode('|', $rawToken, 2);
    if (! ctype_digit($tokenId) || $plainTextToken === '') {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    $tokenRow = DB::table('personal_access_tokens')
        ->where('id', (int) $tokenId)
        ->where('tokenable_type', User::class)
        ->first();

    if (! $tokenRow) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    $hash = hash('sha256', $plainTextToken);
    if (! hash_equals((string) $tokenRow->token, $hash)) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    DB::table('personal_access_tokens')->where('id', (int) $tokenId)->delete();

    return response()->json(['message' => 'Logged out.']);
});

Route::post('/auth/login', function (Request $request) {
    $throttleKey = 'login|'.$request->ip();
    if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
        return response()->json(['message' => 'Too many login attempts. Please try again later.'], 429);
    }

    $username = trim((string) $request->input('username', ''));
    $password = (string) $request->input('password', '');
    $deviceName = trim((string) $request->input('device_name', 'portal'));

    if ($username === '' || $password === '') {
        return response()->json([
            'message' => 'The username and password fields are required.',
            'errors' => [
                'username' => $username === '' ? ['The username field is required.'] : [],
                'password' => $password === '' ? ['The password field is required.'] : [],
            ],
        ], 422);
    }

    $user = DB::table('users')
        ->select(['id', 'name', 'username', 'email', 'password', 'email_verified_at', 'is_suspended'])
        ->where('username', $username)
        ->first();

    if (! $user || ! Hash::check($password, (string) $user->password)) {
        RateLimiter::hit($throttleKey, 60);
        return response()->json(['message' => 'Invalid credentials.'], 401);
    }

    RateLimiter::clear($throttleKey);

    if ((int) $user->is_suspended === 1) {
        return response()->json(['message' => 'Account suspended.'], 403);
    }

    if ($user->email_verified_at === null) {
        return response()->json(['message' => 'Email not verified.'], 403);
    }

    $plainTextToken = (string) config('sanctum.token_prefix', '').Str::random(40);
    $tokenHash = hash('sha256', $plainTextToken);
    
    // Check if token expiration is configured
    $expiration = config('sanctum.expiration');
    $expiresAt = $expiration ? now()->addMinutes($expiration) : null;

    $tokenId = DB::table('personal_access_tokens')->insertGetId([
        'tokenable_type' => User::class,
        'tokenable_id' => $user->id,
        'name' => $deviceName,
        'token' => $tokenHash,
        'abilities' => json_encode(['portal'], JSON_UNESCAPED_SLASHES),
        'expires_at' => $expiresAt,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json([
        'token_type' => 'Bearer',
        'access_token' => $tokenId.'|'.$plainTextToken,
        'expires_at' => $expiresAt?->toISOString(),
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
        ],
    ]);
})->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->post('/plan-applications', [PlanApplicationController::class, 'store']);
