<?php

namespace App\Services;

use App\Enums\ReportStatus;
use App\Events\ReportStatusChanged;
use App\Models\AdminNotification;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ReportNotificationService
{
    public function notifyStatusChanged(Report $report, string $oldStatus, string $newStatus, ?int $actorId = null): void
    {
        $report->loadMissing('category', 'user');

        event(new ReportStatusChanged($report, $oldStatus, $newStatus));

        $new = ReportStatus::tryFromString($newStatus);
        $label = $new?->label() ?? $newStatus;

        $this->storeForAdmins(
            type: 'report_status_changed',
            title: 'Report status updated',
            message: "Report #CC-" . str_pad((string) $report->id, 4, '0', STR_PAD_LEFT)
                . " ({$report->category->name}) is now {$label}.",
            reportId: $report->id,
            exceptUserId: $actorId,
        );

        $citizen = $report->user;
        if (!$citizen?->fcm_token) {
            Log::info('FCM skipped: citizen has no device token', [
                'report_id' => $report->id,
                'user_id' => $citizen?->id,
            ]);
            return;
        }

        $title = 'Report update';
        $body = $new
            ? "Your report \"{$report->category->name}\" is now: {$label}."
            : 'Your report status was updated.';

        FcmService::send($citizen->fcm_token, $title, $body, [
            'type' => 'report_status_changed',
            'report_id' => (string) $report->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]);
    }

    public function notifyAdminsNewReport(Report $report): void
    {
        $report->loadMissing('category');

        $this->storeForAdmins(
            type: 'report_submitted',
            title: 'New report submitted',
            message: "New {$report->category->name} report #CC-"
                . str_pad((string) $report->id, 4, '0', STR_PAD_LEFT)
                . " (priority {$report->priority_score}).",
            reportId: $report->id,
        );

        $admins = User::query()
            ->where('role', 'admin')
            ->whereNotNull('fcm_token')
            ->get();

        if ($admins->isEmpty()) {
            Log::info('FCM skipped: no admin device tokens registered');
            return;
        }

        $title = 'New waste report';
        $body = "New {$report->category->name} report submitted (priority {$report->priority_score}).";

        foreach ($admins as $admin) {
            FcmService::send($admin->fcm_token, $title, $body, [
                'type' => 'report_submitted',
                'report_id' => (string) $report->id,
            ]);
        }
    }

    private function storeForAdmins(
        string $type,
        string $title,
        string $message,
        ?int $reportId = null,
        ?int $exceptUserId = null,
    ): void {
        $admins = User::query()->where('role', 'admin');

        if ($exceptUserId) {
            $admins->where('id', '!=', $exceptUserId);
        }

        foreach ($admins->pluck('id') as $adminId) {
            AdminNotification::create([
                'user_id' => $adminId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'report_id' => $reportId,
            ]);
        }
    }
}
