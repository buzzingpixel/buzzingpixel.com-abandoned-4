<?php

declare(strict_types=1);

namespace Tests\App\Content\PropertyTraits;

use App\Content\PropertyTraits\TextBullets;
use App\Payload\SpecificPayload;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TextBulletsTest extends TestCase
{
    public function testInvalidInstance() : void
    {
        self::expectException(InvalidArgumentException::class);

        self:$this->expectExceptionMessage(
            'Bullet must be a string'
        );

        new class(['textBullets' => [123]]) extends SpecificPayload
        {
            use TextBullets;
        };
    }
}
