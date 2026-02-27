<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

try {
    echo "Starting test...\n";
    $roleName = 'test-role-' . time();
    $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);
    echo "Created role: $roleName\n";

    $perm = Permission::first();
    
    if (!$perm) {
        echo "No permissions found.\n";
        exit(1);
    }
    
    $permId = $perm->id;
    echo "Using Permission ID: $permId (Name: {$perm->name})\n";
    
    // Test 1: Sync with integer ID (simulating the fix)
    echo "Testing sync with integer ID...\n";
    try {
        $role->syncPermissions([(int)$permId]);
        echo "Success: Synced integer ID.\n";
    } catch (\Exception $e) {
        echo "Failed integer sync: " . $e->getMessage() . "\n";
    }
    
    // Test 2: Sync with string ID (simulating the bug)
    echo "Testing sync with string ID...\n";
    try {
        $role->syncPermissions([(string)$permId]);
        echo "Success: Synced string ID (unexpected but okay).\n";
    } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
        echo "Caught expected exception with string ID: " . $e->getMessage() . "\n";
    } catch (\Exception $e) {
        echo "Caught unexpected exception: " . $e->getMessage() . "\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} finally {
    if (isset($role)) {
        $role->delete();
        echo "Cleaned up test role.\n";
    }
}
