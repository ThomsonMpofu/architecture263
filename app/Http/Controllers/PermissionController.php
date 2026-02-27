<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionController extends Controller
{
    public function index(): View
    {
        $permissions = Permission::query()
            ->orderBy('name')
            ->get();

        return view('permissions.index', [
            'permissions' => $permissions,
        ]);
    }

    public function create(): View
    {
        return view('permissions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $guardName = config('auth.defaults.guard', 'web');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions', 'name')->where('guard_name', $guardName)],
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Permission::create([
            'name' => $validated['name'],
            'guard_name' => $guardName,
        ]);

        return redirect()
            ->route('permissions.index')
            ->with('status', 'Permission created successfully.');
    }

    public function show(Permission $permission): View
    {
        return view('permissions.show', [
            'permission' => $permission,
        ]);
    }

    public function edit(Permission $permission): View
    {
        return view('permissions.edit', [
            'permission' => $permission,
        ]);
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $guardName = config('auth.defaults.guard', 'web');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions', 'name')->where('guard_name', $guardName)->ignore($permission->id)],
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permission->update([
            'name' => $validated['name'],
            'guard_name' => $guardName,
        ]);

        return redirect()
            ->route('permissions.edit', $permission)
            ->with('status', 'Permission updated successfully.');
    }
}

