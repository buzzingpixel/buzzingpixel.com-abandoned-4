<?php

declare(strict_types=1);

namespace Tests\App\Content\PropertyTraits;

use App\Content\PropertyTraits\ImageProperties;
use App\Payload\SpecificPayload;
use PHPUnit\Framework\TestCase;

class ImagePropertiesTest extends TestCase
{
    public function testWhenNoInput() : void
    {
        $model = new class() extends SpecificPayload
        {
            use ImageProperties;
        };

        self::assertSame('', $model->getSrc());
        self::assertSame('', $model->getSrcset());
        self::assertSame('', $model->getAlt());
    }

    public function test() : void
    {
        $model = new class([
            'src' => 'TestSrc',
            'srcset' => 'testSrcset',
            'alt' => 'testAlt',
        ]) extends SpecificPayload {
            use ImageProperties;
        };

        self::assertSame('TestSrc', $model->getSrc());
        self::assertSame('testSrcset', $model->getSrcset());
        self::assertSame('testAlt', $model->getAlt());
    }
}