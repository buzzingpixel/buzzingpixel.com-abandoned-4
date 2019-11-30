<?php

declare(strict_types=1);

namespace App\Schedule\Runners;

use App\Schedule\Frequency;
use App\Users\Services\SessionGarbageCollection;

class UserSessionGarbageCollection extends SessionGarbageCollection
{
    public const RUN_EVERY = Frequency::DAY_AT_MIDNIGHT;
}
