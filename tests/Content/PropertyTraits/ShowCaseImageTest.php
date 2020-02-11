<?php

declare(strict_types=1);

namespace Tests\Content\PropertyTraits;

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

        $payload  = $trait->getShowCaseImage();
        $payload2 = $trait->getShowCaseImage();

        self::assertSame($payload, $payload2);
        self::assertSame('', $payload->getOneX());
        self::assertSame('', $payload->getTwoX());
        self::assertSame('', $payload->getAlt());
        self::assertSame([], $payload->getSources());
    }
}
