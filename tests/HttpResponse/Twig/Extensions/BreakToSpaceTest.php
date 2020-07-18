<?php

declare(strict_types=1);

namespace Tests\HttpResponse\Twig\Extensions;

use App\HttpResponse\Twig\Extensions\BreakToSpace;
use PHPUnit\Framework\TestCase;
use Throwable;

use function assert;
use function is_array;

class BreakToSpaceTest extends TestCase
{
    public function testGetFunctions(): void
    {
        $ext = new BreakToSpace();

        $return = $ext->getFunctions();

        self::assertCount(1, $return);

        $twigFunc = $return[0];

        self::assertSame(
            'breakToSpace',
            $twigFunc->getName()
        );

        $callable = $twigFunc->getCallable();

        assert(is_array($callable));

        self::assertCount(2, $callable);

        self::assertSame($ext, $callable[0]);

        self::assertSame('breakToSpaceMethod', $callable[1]);

        self::assertFalse($twigFunc->needsEnvironment());

        self::assertFalse($twigFunc->needsContext());
    }

    /**
     * @throws Throwable
     */
    public function test(): void
    {
        $testStr = 'test1
        test2';

        $ext = new BreakToSpace();

        self::assertSame(
            'test1 test2',
            $ext->breakToSpaceMethod($testStr)
        );
    }
}
