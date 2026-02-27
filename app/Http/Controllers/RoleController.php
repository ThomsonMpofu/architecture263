<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()
            ->withCount('permissions')
            ->orderBy('name')
            ->get();

        return view('roles.index', [
            'roles' => $roles,
        ]);
    }

    public function create(): View
    {
        $permissions = Permission::query()
            ->orderBy('name')
            ->get();

        return view('roles.create', [
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $guardName = config('auth.defaults.guard', 'web');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->where('guard_name', $guardName)],
            'permission_ids' => ['array'],
            'permission_ids.*' => ['integer', Rule::exists('permissions', 'id')],
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => $guardName,
        ]);

        $permissionIds = $validated['permission_ids'] ?? [];
        if (count($permissionIds) > 0) {
            // Ensure IDs are integers, as strings are treated as permission names
            $permissionIds = array_map('intval', $permissionIds);
            $role->syncPermissions($permissionIds);
        }

        return redirect()
            ->route('roles.index')
            ->with('status', 'Role created successfully.');
    }

    public function show(Role $role): View
    {
        $role->load('permissions');

        return view('roles.show', [
            'role' => $role,
        ]);
    }

    public function edit(Role $role): View
    {
        $role->load('permissions');

        $permissions = Permission::query()
            ->orderBy('name')
            ->get();

        return view('roles.edit', [
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $guardName = config('auth.defaults.guard', 'web');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->where('guard_name', $guardName)->ignore($role->id)],
            'permission_ids' => ['array'],
            'permission_ids.*' => ['integer', Rule::exists('permissions', 'id')],
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role->update([
            'name' => $validated['name'],
            'guard_name' => $guardName,
        ]);

        $permissionIds = $validated['permission_ids'] ?? [];
        if (count($permissionIds) > 0) {
            $permissionIds = array_map('intval', $permissionIds);
        }
        $role->syncPermissions($permissionIds);

        return redirect()
            ->route('roles.edit', $role)
            ->with('status', 'Role updated successfully.');
    }
}

