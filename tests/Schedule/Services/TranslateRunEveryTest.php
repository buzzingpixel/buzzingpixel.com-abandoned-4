<?php

declare(strict_types=1);

namespace Tests\Schedule\Services;

use App\Schedule\Frequency;
use App\Schedule\Services\TranslateRunEvery;
use PHPUnit\Framework\TestCase;

class TranslateRunEveryTest extends TestCase
{
    public function testNumericValue() : void
    {
        $val = (new TranslateRunEvery())->getTranslatedValue('5.4');

        self::assertSame(300, $val);
    }

    public function testMappedIntegerValue() : void
    {
        $val = (new TranslateRunEvery())->getTranslatedValue(
            Frequency::HOUR
        );

        self::assertSame(3600, $val);
    }

    public function testMidnightString() : void
    {
        $val = (new TranslateRunEvery())->getTranslatedValue(
            Frequency::FRIDAY_AT_MIDNIGHT
        );

        self::assertSame('fridayatmidnight', $val);
    }
}
