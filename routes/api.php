<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/categories', [ReportController::class, 'categories']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/user/update-token', [AuthController::class, 'updateFcmToken']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/reports', [ReportController::class, 'index']);
    Route::post('/reports', [ReportController::class, 'store']);
    Route::get('/my-reports', [ReportController::class, 'userReports']);
    Route::get('/reports/{id}', [ReportController::class, 'show']);
    Route::get('/reports/{id}/upvote', [ReportController::class, 'upvote']); // Changed from post to get if needed, or leave post - Wait, it was post before:
    Route::post('/reports/{id}/upvote', [ReportController::class, 'upvote']);

    // Chat
    Route::get('/reports/{id}/chat', [\App\Http\Controllers\Api\ChatController::class, 'index']);
    Route::post('/reports/{id}/chat', [\App\Http\Controllers\Api\ChatController::class, 'store']);
    Route::post('/reports/{id}/chat/read', [\App\Http\Controllers\Api\ChatController::class, 'markRead']);
    Route::get('/chat/unread', [\App\Http\Controllers\Api\ChatController::class, 'unreadCount']);
});

Route::get('/run-migrations', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return 'Migrations successfully run!';
    } catch (\Exception $e) {
        return 'Migration error: ' . $e->getMessage();
    }
});

Route::get('/seed-categories', function () {
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'CategorySeeder', '--force' => true]);
    return 'Categories successfully seeded!';
});

Route::get('/clean-categories', function () {
    try {
        $duplicates = \App\Models\Category::all()->groupBy('name');
        $deletedCount = 0;
        foreach ($duplicates as $name => $items) {
            if ($items->count() > 1) {
                $keepId = $items->first()->id;
                $duplicateIds = $items->slice(1)->pluck('id');
                
                // Re-associate any reports linked to duplicate categories to the kept category ID
                \App\Models\Report::whereIn('category_id', $duplicateIds)->update(['category_id' => $keepId]);
                
                // Delete the duplicate categories
                $deletedCount += \App\Models\Category::whereIn('id', $duplicateIds)->delete();
            }
        }
        return "Cleaned up $deletedCount duplicate categories successfully!";
    } catch (\Exception $e) {
        return 'Cleanup error: ' . $e->getMessage();
    }
});


Route::get('/debug-logs', function () {
    $logPath = storage_path('logs/laravel.log');
    if (!file_exists($logPath)) {
        return 'Log file does not exist.';
    }
    $logs = file_get_contents($logPath);
    return nl2br(e(substr($logs, -4000)));
});
