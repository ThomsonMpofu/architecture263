<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OtpLoginController extends Controller
{
    public function show(): View
    {
        return view('auth.otp'); // create this view later if needed
    }

    public function verify(Request $request): RedirectResponse
    {
        // TODO: implement OTP verification logic
        return redirect()->route('dashboard.index');
    }
}