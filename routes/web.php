<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserActivationController;
use App\Http\Controllers\UserInvitationController;
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

    // Admin Invitation Route
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
