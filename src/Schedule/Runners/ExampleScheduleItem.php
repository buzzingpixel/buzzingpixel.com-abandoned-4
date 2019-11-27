<?php

declare(strict_types=1);

namespace App\Schedule\Runners;

use App\Schedule\Frequency;
use const PHP_EOL;

class ExampleScheduleItem
{
    public const RUN_EVERY = Frequency::ALWAYS;

    public function __invoke() : void
    {
        echo self::class . PHP_EOL . PHP_EOL;
    }
}
