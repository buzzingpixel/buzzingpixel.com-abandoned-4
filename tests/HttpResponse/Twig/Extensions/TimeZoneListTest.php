<?php

declare(strict_types=1);

namespace Tests\HttpResponse\Twig\Extensions;

use App\HttpResponse\Twig\Extensions\TimeZoneList;
use App\Utilities\TimeZoneList as TimeZoneListUtility;
use PHPUnit\Framework\TestCase;
use function assert;
use function is_array;

class TimeZoneListTest extends TestCase
{
    public function testGetFunctions() : void
    {
        $ext = new TimeZoneList();

        $return = $ext->getFunctions();

        $twigFunc = $return[0];

        self::assertSame(
            'timeZoneList',
            $twigFunc->getName()
        );

        $callable = $twigFunc->getCallable();

        assert(is_array($callable));

        self::assertCount(2, $callable);

        self::assertSame($ext, $callable[0]);

        self::assertSame('getList', $callable[1]);

        self::assertFalse($twigFunc->needsEnvironment());

        self::assertFalse($twigFunc->needsContext());
    }

    public function testGetList() : void
    {
        self::assertSame(
            TimeZoneListUtility::getList(),
            (new TimeZoneList())->getList(),
        );
    }
}
