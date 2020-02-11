<?php

declare(strict_types=1);

namespace Tests\Content\Modules\ExtractorMethods;

use PHPUnit\Framework\TestCase;
use Tests\TestConfig;
use Throwable;

class ExtractImageCallOutTest extends TestCase
{
    private ExtractImageCallOutImplementation $extractor;

    protected function setUp() : void
    {
        $this->extractor = TestConfig::$di->get(ExtractImageCallOutImplementation::class);
    }

    /**
     * @throws Throwable
     */
    public function testEmptyArrayInput() : void
    {
        $payload = $this->extractor->runTest([]);

        self::assertSame('', $payload->getHeadline());
        self::assertSame('', $payload->getContent());
        self::assertSame([], $payload->getCtas());

        $image = $payload->getImage();
        self::assertSame('', $image->getOneX());
        self::assertSame('', $image->getTwoX());
        self::assertSame('', $image->getAlt());
        self::assertSame([], $image->getSources());
    }

    /**
     * @throws Throwable
     */
    public function testCtasAndImageNotArray() : void
    {
        $payload = $this->extractor->runTest([
            'ctas' => 'FooBar',
            'image' => 'BarBaz',
        ]);

        self::assertSame('', $payload->getHeadline());
        self::assertSame('', $payload->getContent());
        self::assertSame([], $payload->getCtas());

        $image = $payload->getImage();
        self::assertSame('', $image->getOneX());
        self::assertSame('', $image->getTwoX());
        self::assertSame('', $image->getAlt());
        self::assertSame([], $image->getSources());
    }

    /**
     * @throws Throwable
     */
    public function testImageNoSources() : void
    {
        $payload = $this->extractor->runTest([
            'headline' => 'TestHeadline',
            'content' => 'TestContent',
            'ctas' => [
                [
                    'href' => 'TestHref',
                    'content' => 'TestContent',
                ],
                [
                    'href' => 'Foo',
                    '-content' => 'Bar',
                ],
                [
                    '-href' => 'Baz',
                    'content' => 'FooBar',
                ],
            ],
            'image' => [
                '1x' => 'TestSrc',
                '2x' => 'TestSrc2x',
                'alt' => 'TestAlt',
                'sources' => 'FooBar',
            ],
        ]);

        self::assertSame('TestHeadline', $payload->getHeadline());
        self::assertSame("<p>TestContent</p>\n", $payload->getContent());

        $ctas = $payload->getCtas();
        self::assertCount(3, $ctas);
        $cta1 = $ctas[0];
        self::assertSame('TestHref', $cta1->getHref());
        self::assertSame('TestContent', $cta1->getContent());
        $cta2 = $ctas[1];
        self::assertSame('Foo', $cta2->getHref());
        self::assertSame('', $cta2->getContent());
        $cta3 = $ctas[2];
        self::assertSame('', $cta3->getHref());
        self::assertSame('FooBar', $cta3->getContent());

        $image = $payload->getImage();
        self::assertSame('TestSrc', $image->getOneX());
        self::assertSame('TestSrc2x', $image->getTwoX());
        self::assertSame('TestAlt', $image->getAlt());
        self::assertSame([], $image->getSources());
    }

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $payload = $this->extractor->runTest([
            'headline' => 'TestHeadline',
            'content' => 'TestContent',
            'ctas' => [
                [
                    'href' => 'TestHref',
                    'content' => 'TestContent',
                ],
            ],
            'image' => [
                '1x' => 'TestSrc',
                '2x' => 'TestSrc2x',
                'alt' => 'TestAlt',
                'sources' => [
                    [
                        '1x' => 'Foo',
                        '2x' => 'Bar',
                        'mediaQuery' => 'Baz',
                    ],
                    [
                        '1x' => 'Baz',
                        '2x' => 'BarFoo',
                        'mediaQuery' => 'fooBar',
                    ],
                ],
            ],
        ]);

        self::assertSame('TestHeadline', $payload->getHeadline());
        self::assertSame("<p>TestContent</p>\n", $payload->getContent());

        $ctas = $payload->getCtas();
        self::assertCount(1, $ctas);
        $cta1 = $ctas[0];
        self::assertSame('TestHref', $cta1->getHref());
        self::assertSame('TestContent', $cta1->getContent());

        $image = $payload->getImage();
        self::assertSame('TestSrc', $image->getOneX());
        self::assertSame('TestSrc2x', $image->getTwoX());
        self::assertSame('TestAlt', $image->getAlt());

        $sources = $image->getSources();
        self::assertCount(2, $sources);

        $source1 = $sources[0];
        self::assertSame('Foo', $source1->getOneX());
        self::assertSame('Bar', $source1->getTwoX());
        self::assertSame('Baz', $source1->getMediaQuery());

        $source2 = $sources[1];
        self::assertSame('Baz', $source2->getOneX());
        self::assertSame('BarFoo', $source2->getTwoX());
        self::assertSame('fooBar', $source2->getMediaQuery());
    }
}
