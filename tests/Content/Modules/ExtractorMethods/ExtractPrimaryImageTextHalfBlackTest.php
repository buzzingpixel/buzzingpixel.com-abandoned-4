<?php

declare(strict_types=1);

namespace Tests\Content\Modules\ExtractorMethods;

use PHPUnit\Framework\TestCase;
use Tests\TestConfig;
use Throwable;

class ExtractPrimaryImageTextHalfBlackTest extends TestCase
{
    /** @var ExtractPrimaryImageTextHalfBlackImplementation */
    private $extractor;

    protected function setUp() : void
    {
        $this->extractor = TestConfig::$di->get(ExtractPrimaryImageTextHalfBlackImplementation::class);
    }

    /**
     * @throws Throwable
     */
    public function testWithEmptyArrayInput() : void
    {
        $payload = $this->extractor->runTest([]);

        self::assertSame('', $payload->getHeadline());
        self::assertSame('', $payload->getContent());

        $image = $payload->getImage();
        self::assertSame('', $image->getOneX());
        self::assertSame('', $image->getTwoX());
        self::assertSame('', $image->getAlt());
        self::assertSame([], $image->getSources());
    }

    /**
     * @throws Throwable
     */
    public function testWhenImageIsNotArray() : void
    {
        $payload = $this->extractor->runTest(['image' => 'FooBar']);

        self::assertSame('', $payload->getHeadline());
        self::assertSame('', $payload->getContent());

        $image = $payload->getImage();
        self::assertSame('', $image->getOneX());
        self::assertSame('', $image->getTwoX());
        self::assertSame('', $image->getAlt());
        self::assertSame([], $image->getSources());
    }

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $payload = $this->extractor->runTest([
            'headline' => 'HeadlineFooBar',
            'image' => [
                '1x' => 'TestOneX',
                '2x' => 'TestTwoX',
                'alt' => 'TestAlt',
                'sources' => [
                    [
                        'foo' => 'bar',
                        'baz' => 'foo',
                    ],
                    [
                        '1x' => 'Source1x',
                        '2x' => 'Source2x',
                        'mediaQuery' => 'SourceMediaQuery',
                    ],
                ],
            ],
            'content' => 'ContentFooBar',
        ]);

        self::assertSame('HeadlineFooBar', $payload->getHeadline());
        self::assertSame("<p>ContentFooBar</p>\n", $payload->getContent());

        $image = $payload->getImage();
        self::assertSame('TestOneX', $image->getOneX());
        self::assertSame('TestTwoX', $image->getTwoX());
        self::assertSame('TestAlt', $image->getAlt());

        $sources = $image->getSources();
        self::assertCount(2, $sources);

        $source1 = $sources[0];
        self::assertSame('', $source1->getOneX());
        self::assertSame('', $source1->getTwoX());
        self::assertSame('', $source1->getMediaQuery());

        $source2 = $sources[1];
        self::assertSame('Source1x', $source2->getOneX());
        self::assertSame('Source2x', $source2->getTwoX());
        self::assertSame('SourceMediaQuery', $source2->getMediaQuery());
    }
}
