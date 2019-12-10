<?php

declare(strict_types=1);

namespace Tests\HttpResponse\Twig\Extensions;

use App\HttpResponse\Twig\Extensions\PhpFunctions;
use PHPUnit\Framework\TestCase;

class PhpFunctionsTest extends TestCase
{
    public function testGetFunctions() : void
    {
        $extension = new PhpFunctions();

        $functions = $extension->getFunctions();

        self::assertCount(3, $functions);

        $getClassFunction = $functions[0];
        self::assertSame('get_class', $getClassFunction->getName());
        self::assertSame('get_class', $getClassFunction->getCallable());

        $getTypeFunction = $functions[1];
        self::assertSame('gettype', $getTypeFunction->getName());
        self::assertSame('gettype', $getTypeFunction->getCallable());

        $uniqidFunction = $functions[2];
        self::assertSame('uniqid', $uniqidFunction->getName());
        self::assertSame('uniqid', $uniqidFunction->getCallable());
    }
}
