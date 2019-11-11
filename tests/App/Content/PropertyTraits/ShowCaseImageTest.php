<?php

declare(strict_types=1);

namespace Tests\App\Content\PropertyTraits;

use App\Content\Modules\Payloads\ImagePayload;
use App\Content\PropertyTraits\ShowCaseImage;
use PHPUnit\Framework\TestCase;

class ShowCaseImageTest extends TestCase
{
    public function testNoShowCaseImage() : void
    {
        $trait = new class()
        {
            use ShowCaseImage;
        };

        /** @var ImagePayload $payload */
        $payload = $trait->getShowCaseImage();
        /** @var ImagePayload $payl$payload2oad */
        $payload2 = $trait->getShowCaseImage();

        self::assertInstanceOf(ImagePayload::class, $payload);
        self::assertSame($payload, $payload2);
        self::assertSame('', $payload->getOneX());
        self::assertSame('', $payload->getTwoX());
        self::assertSame('', $payload->getAlt());
        self::assertSame([], $payload->getSources());
    }
}
