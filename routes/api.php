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

Route::get('/debug-storage', function () {
    return response()->json([
        'files_in_reports' => \Illuminate\Support\Facades\Storage::disk('public')->files('reports'),
        'disk_root' => config('filesystems.disks.public.root'),
        'storage_path' => storage_path('app/public'),
        'symlink_exists' => file_exists(public_path('storage')),
    ]);
});

Route::get('/fix-storage', function () {
    try {
        if (file_exists(public_path('storage'))) {
            unlink(public_path('storage'));
        }
        \Illuminate\Support\Facades\Artisan::call('storage:link', ['--force' => true]);
        return response()->json([
            'status' => 'success',
            'message' => 'Storage link created successfully!',
            'output' => \Illuminate\Support\Facades\Artisan::output()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

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
