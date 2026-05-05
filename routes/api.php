<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\AdminReportController;
use App\Http\Controllers\Api\NotificationController;

/*
|--------------------------------------------------------------------------
| AUTH Routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected Routes (requires Sanctum auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // user profile
    Route::post('/user/update', [AuthController::class, 'updateProfile']);

    // report user
    Route::post('/reports',      [ReportController::class, 'store']);
    Route::get('/reports',       [ReportController::class, 'index']);
    Route::get('/reports/{id}',  [ReportController::class, 'show']);

    // notifikasi
    Route::get('/notifications',              [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read',    [NotificationController::class, 'markAsRead']);

    // admin route
    Route::middleware('is_admin')->prefix('admin')->group(function () {
        Route::get('/reports',                [AdminReportController::class, 'index']);
        Route::get('/reports/{id}',           [AdminReportController::class, 'show']);
        Route::put('/reports/{id}',           [AdminReportController::class, 'update']);
        Route::post('/reports/{id}/complete', [AdminReportController::class, 'complete']);
    });
});
