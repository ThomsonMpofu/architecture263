<?php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

try {
    $roleName = 'test-role-' . time();
    $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);
    $perm = Permission::first();
    
    if (!$perm) {
        echo "No permissions found.\n";
        exit(1);
    }
    
    $permId = $perm->id;
    echo "Using Permission ID: $permId\n";
    
    // Test 1: Sync with integer ID (simulating the fix)
    echo "Testing sync with integer ID...\n";
    $role->syncPermissions([(int)$permId]);
    echo "Success: Synced integer ID.\n";
    
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
