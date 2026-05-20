<?php

namespace App\Providers;

use App\Models\AdminNotification;
use Illuminate\Support\Facades\View;

class AdminViewComposer
{
    public static function register(): void
    {
        View::composer('layouts.admin', function ($view) {
            if (!auth()->check()) {
                return;
            }

            $view->with('adminNotifications', AdminNotification::query()
                ->where('user_id', auth()->id())
                ->with('report')
                ->orderByDesc('created_at')
                ->limit(15)
                ->get());

            $view->with('unreadNotificationCount', AdminNotification::query()
                ->where('user_id', auth()->id())
                ->unread()
                ->count());
        });
    }
}
