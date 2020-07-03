<?php

declare(strict_types=1);

namespace App\Licenses\Services;

use App\Licenses\Models\LicenseModel;
use App\Utilities\SystemClock;

class LicenseStatus
{
    private SystemClock $clock;

    public function __construct(SystemClock $clock)
    {
        $this->clock = $clock;
    }

    public function daysUntilExpiration(LicenseModel $license): ?int
    {
        if ($license->expires === null) {
            return null;
        }

        $currentTime = $this->clock->getCurrentTime();

        $diff = $license->expires->diff($currentTime);

        if ($license->expires->getTimestamp() < $currentTime->getTimestamp()) {
            /** @phpstan-ignore-next-line */
            return (int) -$diff->days;
        }

        return (int) $diff->days;
    }

    public function isExpired(LicenseModel $license): bool
    {
        return $this->daysUntilExpiration($license) < 0;
    }

    public function statusString(LicenseModel $license): string
    {
        if ($license->isDisabled) {
            return 'disabled';
        }

        $days = $this->daysUntilExpiration($license);

        if ($days === null) {
            return 'active';
        }

        if ($days < 0) {
            return 'expired';
        }

        if ($days < 1) {
            return 'expires today!';
        }

        if ($days < 8) {
            return 'expires in ' . $days . ' days';
        }

        return 'active';
    }
}
