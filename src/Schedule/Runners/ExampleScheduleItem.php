<?php

declare(strict_types=1);

namespace App\Schedule\Runners;

use App\Schedule\Frequency;

class ExampleScheduleItem
{
    public const RUN_EVERY = Frequency::FIVE_MINUTES;

    public function __invoke() : void
    {
    }
}
