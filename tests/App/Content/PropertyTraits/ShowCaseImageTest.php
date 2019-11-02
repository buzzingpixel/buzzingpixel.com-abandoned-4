<?php

declare(strict_types=1);

namespace Tests\App\Content\PropertyTraits;

use App\Content\Modules\Payloads\ShowCaseImagePayload;
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

        /** @var ShowCaseImagePayload $payload */
        $payload = $trait->getShowCaseImage();

        self::assertInstanceOf(ShowCaseImagePayload::class, $payload);
        self::assertSame('', $payload->getSrc());
        self::assertSame('', $payload->getSrcset());
        self::assertSame('', $payload->getAlt());
        self::assertSame([], $payload->getSources());
    }
}
