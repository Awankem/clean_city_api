<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Priority Screen: Sign Up (Admin Default)
Route::get('/', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/', [RegisterController::class, 'register']);

// Secondary Screen: Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Admin Dashboard
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Reports
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports.index');
    Route::get('/reports/export', [DashboardController::class, 'exportReports'])->name('reports.export');
    Route::get('/reports/{id}', [DashboardController::class, 'showReport'])->name('reports.show');
    Route::post('/reports/{id}/status', [DashboardController::class, 'updateStatus'])->name('reports.update-status');

    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
    Route::get('/analytics/export', [DashboardController::class, 'exportAnalytics'])->name('analytics.export');
    Route::get('/hotspots', [DashboardController::class, 'hotspots'])->name('hotspots');
    Route::get('/audit', [DashboardController::class, 'audit'])->name('audit');
    Route::get('/audit/export', [DashboardController::class, 'exportAudit'])->name('audit.export');

    Route::post('/notifications/read-all', [AdminNotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [AdminNotificationController::class, 'markRead'])->name('notifications.read');

});
