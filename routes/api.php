<?php

use App\Http\Controllers\Api\ArchitectController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EngagementController;
use App\Http\Controllers\Api\PlanApplicationController;
use App\Http\Controllers\Api\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [RegistrationController::class, 'store'])->middleware('throttle:5,1');
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/architects', [ArchitectController::class, 'index']);
    Route::get('/architects/{id}', [ArchitectController::class, 'show']);
    Route::post('/architect/blue-book/request', [ArchitectController::class, 'requestBlueBook']);

    Route::get('/engagements', [EngagementController::class, 'index']);
    Route::post('/engagements', [EngagementController::class, 'store']);
    Route::get('/engagements/{id}', [EngagementController::class, 'show']);
    Route::post('/engagements/{id}/approve', [EngagementController::class, 'approve']);
    Route::post('/engagements/{id}/contract/sign', [EngagementController::class, 'signContract']);

    Route::get('/plan-applications', [PlanApplicationController::class, 'index']);
    Route::post('/plan-applications', [PlanApplicationController::class, 'store']);
    Route::post('/plan-applications/draft', [PlanApplicationController::class, 'saveDraft']);
    Route::get('/plan-applications/draft/{engagementId}', [PlanApplicationController::class, 'getDraft']);
    Route::get('/plan-applications/{id}', [PlanApplicationController::class, 'show']);
    Route::post('/plan-applications/{id}/finalize', [PlanApplicationController::class, 'finalize']);
    Route::post('/plan-applications/{id}/resubmit', [PlanApplicationController::class, 'resubmit']);
    Route::get('/plan-applications/{id}/drawings', [PlanApplicationController::class, 'downloadDrawings']);
    Route::get('/plan-applications/{id}/drawings/{version}', [PlanApplicationController::class, 'downloadDrawingVersion'])->whereNumber('version');
    Route::post('/plan-applications/{id}/comments', [PlanApplicationController::class, 'storeComment']);
    Route::get('/plan-applications/{id}/markups', [PlanApplicationController::class, 'markups']);
    Route::post('/plan-applications/{id}/markups', [PlanApplicationController::class, 'storeMarkup']);
    Route::delete('/plan-applications/{id}/markups/{markupId}', [PlanApplicationController::class, 'destroyMarkup']);
    Route::post('/plan-applications/{id}/decide', [PlanApplicationController::class, 'decide'])->middleware('throttle:20,1');
});
