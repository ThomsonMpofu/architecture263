<?php

use App\Http\Controllers\ProfileController;

use App\Http\Controllers\AccessControlController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserInvitationController;
use App\Http\Controllers\UserActivationController;
use App\Http\Controllers\DocumentController;

use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';


Route::get('/document',[DocumentController::class,'index'])->name('documents.index');
Route::resource('documents', DocumentController::class);
Route::get('/document/download/{id}',[DocumentController::class,'download'])->name('documents.download');


Route::get('/', function () {
    return redirect()->route('login');
});

// User Activation Routes (Public)
Route::get('/activate/{token}', [UserActivationController::class, 'show'])->name('activate.show');
Route::post('/activate/{token}', [UserActivationController::class, 'store'])->name('activate.store');

Route::get('/dashboard', function () {
    return view('dashboard.index');
})->middleware(['auth', 'session.timeout'])->name('dashboard.index');

Route::middleware(['auth', 'session.timeout'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');

    Route::get('/access-control', [AccessControlController::class, 'index'])->name('access-control.index');

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');

    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show');
    Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    
    Route::get('/users/invite', [UserInvitationController::class, 'create'])->name('users.invite.create');
    Route::post('/users/invite', [UserInvitationController::class, 'store'])->name('users.invite');
    Route::post('/users/resend/{id}', [UserInvitationController::class, 'resend'])->name('users.resend');

    // User Management Actions
    Route::put('/users/{id}', [UserInvitationController::class, 'update'])->name('users.update');
    Route::post('/users/{id}/toggle-suspend', [UserInvitationController::class, 'toggleSuspend'])->name('users.toggle-suspend');
    Route::post('/users/{id}/expire-link', [UserInvitationController::class, 'expireLink'])->name('users.expire-link');
    Route::post('/users/{id}/reactivate-link', [UserInvitationController::class, 'reactivateLink'])->name('users.reactivate-link');
});

// Route::post('login', [AuthenticatedSessionController::class, 'store']);
