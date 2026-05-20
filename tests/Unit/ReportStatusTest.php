<?php

namespace Tests\Unit;

use App\Enums\ReportStatus;
use PHPUnit\Framework\TestCase;

class ReportStatusTest extends TestCase
{
    public function test_forward_only_transitions(): void
    {
        $pending = ReportStatus::Pending;
        $inProgress = ReportStatus::InProgress;
        $resolved = ReportStatus::Resolved;

        $this->assertTrue($pending->canTransitionTo($inProgress));
        $this->assertFalse($pending->canTransitionTo($resolved));
        $this->assertFalse($pending->canTransitionTo($pending));

        $this->assertTrue($inProgress->canTransitionTo($resolved));
        $this->assertFalse($inProgress->canTransitionTo($pending));

        $this->assertSame([], $resolved->allowedNext());
        $this->assertFalse($resolved->canTransitionTo($pending));
        $this->assertFalse($resolved->canTransitionTo($inProgress));
    }
}
