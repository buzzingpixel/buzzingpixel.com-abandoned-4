<?php

declare(strict_types=1);

namespace Tests\App\Content\PropertyTraits;

use App\Content\Modules\Payloads\ImagePayload;
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

        /** @var ImagePayload $payload */
        $payload = $trait->getImage();
        /** @var ImagePayload $payl$payload2oad */
        $payload2 = $trait->getImage();

        self::assertInstanceOf(ImagePayload::class, $payload);
        self::assertSame($payload, $payload2);
        self::assertSame('', $payload->getOneX());
        self::assertSame('', $payload->getTwoX());
        self::assertSame('', $payload->getAlt());
        self::assertSame([], $payload->getSources());
    }
}
