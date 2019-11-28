<?php

declare(strict_types=1);

namespace App\Schedule\Runners;

use App\Schedule\Frequency;
use function dump;

class ExampleScheduleItem
{
    public const RUN_EVERY = Frequency::FIVE_MINUTES;

    public function __invoke() : void
    {
        dump(self::class);
    }
}
