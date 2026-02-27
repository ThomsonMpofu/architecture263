<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->with('roles')
            ->orderByDesc('created_at')
            ->get();

        return view('users.index', [
            'users' => $users,
        ]);
    }

    public function create(): View
    {
        $roles = Role::query()
            ->orderBy('name')
            ->get();

        return view('users.create', [
            'roles' => $roles,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['nullable', 'integer', Rule::exists('roles', 'id')],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        if (! empty($validated['role_id'])) {
            $role = Role::find($validated['role_id']);
            if ($role) {
                $user->syncRoles([$role]);
            }
        } else {
            $defaultRole = Role::where('name', 'user')
                ->where('guard_name', config('auth.defaults.guard', 'web'))
                ->first();

            if ($defaultRole) {
                $user->syncRoles([$defaultRole]);
            }
        }

        return redirect()
            ->route('users.index')
            ->with('status', 'User created successfully.');
    }
}

