<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserActivationController extends Controller
{
    /**
     * Show the activation form.
     */
    public function show(Request $request, $token)
    {
        $email = $request->query('email');

        // Verify token
        $invitation = DB::table('user_invitations')
            ->where('token', $token)
            ->where('email', $email)
            ->first();

        if (!$invitation) {
            abort(404, 'Invalid or expired activation link.');
        }

        return view('auth.activate', ['token' => $token, 'email' => $email]);
    }

    /**
     * Handle the activation request.
     */
    public function store(Request $request, $token)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verify token again
        $invitation = DB::table('user_invitations')
            ->where('token', $token)
            ->where('email', $request->email)
            ->first();

        if (!$invitation) {
            return back()->withErrors(['email' => 'Invalid or expired activation link.']);
        }

        DB::beginTransaction();

        try {
            // Update User Password
            DB::table('users')
                ->where('email', $request->email)
                ->update([
                    'password' => Hash::make($request->password),
                    'email_verified_at' => now(),
                    'updated_at' => now(),
                ]);

            // Delete Invitation
            DB::table('user_invitations')
                ->where('email', $request->email)
                ->delete();

            DB::commit();

            // Log the user in? Or redirect to login?
            // "Login uses Username + Password (not email)"
            // So we redirect to login page with a success message.
            return redirect()->route('login')->with('status', 'Account activated! Please login with your username and password.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['email' => 'Activation failed: ' . $e->getMessage()]);
        }
    }
}
