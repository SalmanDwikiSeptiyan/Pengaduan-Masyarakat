<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RekapController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MapController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to admin login
Route::get('/', function () {
    return redirect()->route('admin.login');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {

    // Auth (Guest)
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');

    // Protected (Auth + Admin)
    Route::middleware(['auth', 'is_admin'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
        Route::get('/reports/{id}', [ReportController::class, 'show'])->name('admin.reports.show');
        Route::put('/reports/{id}/status', [ReportController::class, 'updateStatus'])->name('admin.reports.updateStatus');
        Route::post('/reports/{id}/complete', [ReportController::class, 'complete'])->name('admin.reports.complete');
        Route::delete('/reports/{id}', [ReportController::class, 'destroy'])->name('admin.reports.destroy');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/{id}', [UserController::class, 'show'])->name('admin.users.show');

        // Peta Sebaran
        Route::get('/map', [MapController::class, 'index'])->name('admin.map');

        // Rekap & Export
        Route::get('/rekap', [RekapController::class, 'index'])->name('admin.rekap');
        Route::get('/rekap/export-report', [RekapController::class, 'exportReport'])->name('admin.rekap.report');
    });
});

