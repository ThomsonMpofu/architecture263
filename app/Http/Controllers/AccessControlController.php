<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AccessControlController extends Controller
{
    public function index(): View
    {
        return view('access-control.index', [
            'rolesCount' => Role::count(),
            'permissionsCount' => Permission::count(),
        ]);
    }
}

