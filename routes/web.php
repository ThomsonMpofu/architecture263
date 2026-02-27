<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AccessControlController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserInvitationController;
use App\Http\Controllers\UserActivationController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/', function () {
    return redirect()->route('login');
});

// User Activation Routes (Public)
Route::get('/activate/{token}', [UserActivationController::class, 'show'])->name('activate.show');
Route::post('/activate/{token}', [UserActivationController::class, 'store'])->name('activate.store');

Route::get('/dashboard', function () {
    return view('dashboard.index');
})->middleware('auth')->name('dashboard.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/users', [UserController::class, 'index'])
        ->middleware('permission:users.view')
        ->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])
        ->middleware('permission:users.create')
        ->name('users.create');
        
    Route::post('/users', [UserController::class, 'store'])
        ->middleware('permission:users.create')
        ->name('users.store');

    Route::get('/access-control', [AccessControlController::class, 'index'])
        ->middleware('permission:roles.view|permissions.view')
        ->name('access-control.index');

    Route::get('/roles', [RoleController::class, 'index'])
        ->middleware('permission:roles.view')
        ->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])
        ->middleware('permission:roles.create')
        ->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])
        ->middleware('permission:roles.create')
        ->name('roles.store');
    Route::get('/roles/{role}', [RoleController::class, 'show'])
        ->middleware('permission:roles.view')
        ->name('roles.show');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
        ->middleware('permission:roles.edit')
        ->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])
        ->middleware('permission:roles.edit')
        ->name('roles.update');

    Route::get('/permissions', [PermissionController::class, 'index'])
        ->middleware('permission:permissions.view')
        ->name('permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])
        ->middleware('permission:permissions.create')
        ->name('permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])
        ->middleware('permission:permissions.create')
        ->name('permissions.store');
    Route::get('/permissions/{permission}', [PermissionController::class, 'show'])
        ->middleware('permission:permissions.view')
        ->name('permissions.show');
    Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])
        ->middleware('permission:permissions.edit')
        ->name('permissions.edit');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])
        ->middleware('permission:permissions.edit')
        ->name('permissions.update');
    
    Route::get('/users/invite', [UserInvitationController::class, 'create'])
        ->middleware('permission:users.invite')
        ->name('users.invite.create');
    Route::post('/users/invite', [UserInvitationController::class, 'store'])
        ->middleware('permission:users.invite')
        ->name('users.invite');
    Route::post('/users/resend/{id}', [UserInvitationController::class, 'resend'])
        ->middleware('permission:users.resend')
        ->name('users.resend');
});

// Route::post('login', [AuthenticatedSessionController::class, 'store']);
