<?php

declare(strict_types=1);

namespace App\Schedule\Runners;

use App\Schedule\Frequency;
use App\Users\Services\ResetTokenGarbageCollection;

class UserResetTokenGarbageCollection extends ResetTokenGarbageCollection
{
    public const RUN_EVERY = Frequency::FIVE_MINUTES;
}
