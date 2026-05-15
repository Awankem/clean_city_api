<?php

use Illuminate\Support\Facades\Route;
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
    Route::get('/reports/{id}', [DashboardController::class, 'showReport'])->name('reports.show');
    Route::post('/reports/{id}/status', [DashboardController::class, 'updateStatus'])->name('reports.update-status');

    // New Modules
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
    Route::get('/hotspots', [DashboardController::class, 'hotspots'])->name('hotspots');
    Route::get('/audit', [DashboardController::class, 'audit'])->name('audit');

});
