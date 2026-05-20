<?php

namespace App\Events;

use App\Models\Report;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Report $report,
        public string $oldStatus,
        public string $newStatus,
    ) {
        $this->report = $report->load('category');
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('hotspots'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'report.status.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'report' => $this->report,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ];
    }
}
