<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Activity Tracker routes — all protected by auth middleware.
| Admin routes additionally use the AdminMiddleware.
|
*/

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// ── Authenticated Routes ─────────────────────────────────────────────

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard — Today's activity overview
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Daily Activity View
    Route::get('/daily/{date?}', [ActivityLogController::class, 'dailyView'])->name('daily.show');

    // Activity Log (status updates)
    Route::post('/activities/{activity}/log', [ActivityLogController::class, 'store'])->name('activity-logs.store');

    // Activities — Read-only for members
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports', [ReportController::class, 'generate'])->name('reports.generate');
    Route::post('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // ── Admin-only Routes ────────────────────────────────────────────
    Route::middleware(AdminMiddleware::class)->group(function () {
        Route::get('/activities/create', [ActivityController::class, 'create'])->name('activities.create');
        Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
        Route::get('/activities/{activity}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
        Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
        Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');
    });

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
