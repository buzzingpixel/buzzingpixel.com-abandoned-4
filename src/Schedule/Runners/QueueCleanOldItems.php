<?php

declare(strict_types=1);

namespace App\Schedule\Runners;

use App\Queue\Services\CleanOldItems;
use App\Schedule\Frequency;

class QueueCleanOldItems extends CleanOldItems
{
    public const RUN_EVERY = Frequency::DAY_AT_MIDNIGHT;
}
