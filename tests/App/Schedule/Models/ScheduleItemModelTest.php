<?php

declare(strict_types=1);

namespace Tests\App\Schedule\Models;

use App\Schedule\Models\ScheduleItemModel;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Throwable;

class ScheduleItemModelTest extends TestCase
{
    public function testSetInvalidType() : void
    {
        $exception = null;

        try {
            new ScheduleItemModel([
                'runEvery' => new stdClass(),
            ]);
        } catch (Throwable $e) {
            $exception = $e;
        }

        self::assertInstanceOf(
            InvalidArgumentException::class,
            $exception
        );

        self::assertSame(
            'RunEvery must be a float, integer, or string',
            $exception->getMessage()
        );
    }
}
