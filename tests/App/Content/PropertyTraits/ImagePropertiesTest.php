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

        self::assertSame('', $model->getOneX());
        self::assertSame('', $model->getTwoX());
        self::assertSame('', $model->getAlt());
    }

    public function test() : void
    {
        $model = new class([
            'oneX' => 'TestSrc',
            'twoX' => 'testSrcset',
            'alt' => 'testAlt',
        ]) extends SpecificPayload {
            use ImageProperties;
        };

        self::assertSame('TestSrc', $model->getOneX());
        self::assertSame('testSrcset', $model->getTwoX());
        self::assertSame('testAlt', $model->getAlt());
    }
}
