<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guardName = config('auth.defaults.guard', 'web');
        $permissions = [
            'users.view',
            'users.create',
            'users.invite',
            'users.resend',
            'roles.view',
            'roles.create',
            'roles.edit',
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            // New Sidebar Permissions
            'professionals_registry.view',
            'professionals.view',
            'firms.view',
            'registry_applications.view',
            'certificates.view',
            'communications.view',
            'notifications.view',
            'communication_templates.view',
            'communication_logs.view',
            'branding.view',
            'access_control.view',
        ];

        $permissionModels = [];
        foreach ($permissions as $permission) {
            $permissionModels[] = Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guardName,
            ]);
        }

        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guardName]);
        $role->syncPermissions($permissionModels);

        Role::firstOrCreate(['name' => 'user', 'guard_name' => $guardName]);

        // Create Admin User
        $user = User::firstOrCreate(
            ['email' => 'admin@architecture263.com'],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign Role
        $user->assignRole($role);
    }
}
