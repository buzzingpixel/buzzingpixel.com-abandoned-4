<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Factories\ValidationFactory;
use PHPUnit\Framework\TestCase;

class ValidationFactoryTest extends TestCase
{
    public function testEmptyArray() : void
    {
        $factory = new ValidationFactory();

        $validator = $factory();

        self::assertFalse($validator->getShowValidationRules());

        self::assertSame(
            [],
            $validator->getDefaultMessages(),
        );
    }

    public function test() : void
    {
        $factory = new ValidationFactory();

        $validator = $factory(['foo' => 'bar']);

        self::assertFalse($validator->getShowValidationRules());

        self::assertSame(
            ['foo' => 'bar'],
            $validator->getDefaultMessages(),
        );
    }
}
