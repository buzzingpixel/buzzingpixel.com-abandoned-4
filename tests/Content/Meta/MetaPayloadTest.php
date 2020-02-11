<?php

declare(strict_types=1);

namespace Tests\Content\Meta;

use App\Content\Meta\MetaPayload;
use PHPUnit\Framework\TestCase;

class MetaPayloadTest extends TestCase
{
    public function testMetaPayload() : void
    {
        $originalPayload = new MetaPayload();

        $withTitle = $originalPayload->withMetaTitle(
            'Test Meta Title'
        );

        self::assertNotSame($originalPayload, $withTitle);

        self::assertSame(
            'Test Meta Title',
            $withTitle->getMetaTitle()
        );

        $withMetaDescription = $withTitle->withMetaDescription(
            'Test Desc'
        );

        self::assertNotSame($withTitle, $withMetaDescription);

        self::assertSame(
            'Test Meta Title',
            $withMetaDescription->getMetaTitle()
        );

        self::assertSame(
            'Test Desc',
            $withMetaDescription->getMetaDescription()
        );
    }
}
