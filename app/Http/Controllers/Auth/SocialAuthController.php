<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SocialAuthController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        // TODO: implement Socialite redirect
        return redirect('/login');
    }

    public function callback(string $provider, Request $request): RedirectResponse
    {
        // TODO: implement Socialite callback
        return redirect('/dashboard');
    }
}