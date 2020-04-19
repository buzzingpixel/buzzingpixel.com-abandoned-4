<?php

declare(strict_types=1);

namespace App\Schedule\Runners;

use App\Queue\Services\CleanDeadItems;
use App\Schedule\Frequency;

class QueueCleanDeadItems extends CleanDeadItems
{
    public const RUN_EVERY = Frequency::ALWAYS;
}
