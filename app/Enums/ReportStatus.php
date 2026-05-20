<?php

namespace App\Enums;

enum ReportStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending review',
            self::InProgress => 'In review',
            self::Resolved => 'Resolved',
        };
    }

    /**
     * Allowed forward-only transitions. Terminal at resolved.
     */
    public function allowedNext(): array
    {
        return match ($this) {
            self::Pending => [self::InProgress],
            self::InProgress => [self::Resolved],
            self::Resolved => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedNext(), true);
    }

    public static function tryFromString(string $value): ?self
    {
        return self::tryFrom($value);
    }
}
