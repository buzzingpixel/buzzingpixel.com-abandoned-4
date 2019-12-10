<?php

declare(strict_types=1);

namespace Tests\App\Utilities;

use App\Utilities\SystemClock;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class SystemClockTest extends TestCase
{
    public function testGetCurrentTime() : void
    {
        $dateTimeFromService = (new SystemClock())->getCurrentTime();

        $dateTime = new DateTimeImmutable();

        self::assertSame(
            $dateTime->format('Y-m-d H:i:s'),
            $dateTimeFromService->format('Y-m-d H:i:s')
        );
    }
}
