<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::check()) {
            $lifetimeMinutes = (int) config('session.lifetime', 120);
            $lastActivity = $request->session()->get('last_activity_at');
            $now = time();

            if (is_int($lastActivity) && ($now - $lastActivity) > ($lifetimeMinutes * 60)) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()
                    ->route('login')
                    ->with('status', 'Session expired. Please login again.');
            }

            $request->session()->put('last_activity_at', $now);
        }

        return $next($request);
    }
}

