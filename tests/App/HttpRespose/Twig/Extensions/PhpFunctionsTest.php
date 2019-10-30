<?php

declare(strict_types=1);

namespace Tests\App\HttpRespose\Twig\Extensions;

use App\HttpRespose\Twig\Extensions\PhpFunctions;
use PHPUnit\Framework\TestCase;

class PhpFunctionsTest extends TestCase
{
    public function testGetFunctions() : void
    {
        $extension = new PhpFunctions();

        $functions = $extension->getFunctions();

        self::assertCount(2, $functions);

        $getClassFunction = $functions[0];
        self::assertSame('get_class', $getClassFunction->getName());
        self::assertSame('get_class', $getClassFunction->getCallable());

        $uniqidFunction = $functions[1];
        self::assertSame('uniqid', $uniqidFunction->getName());
        self::assertSame('uniqid', $uniqidFunction->getCallable());
    }
}
