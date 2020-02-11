<?php

declare(strict_types=1);

namespace Tests\Content\PropertyTraits;

use App\Content\PropertyTraits\Image;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    public function testNoImage() : void
    {
        $trait = new class()
        {
            use Image;
        };

        $payload  = $trait->getImage();
        $payload2 = $trait->getImage();

        self::assertSame($payload, $payload2);
        self::assertSame('', $payload->getOneX());
        self::assertSame('', $payload->getTwoX());
        self::assertSame('', $payload->getAlt());
        self::assertSame([], $payload->getSources());
    }
}
