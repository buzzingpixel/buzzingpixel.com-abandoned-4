<?php

declare(strict_types=1);

namespace Tests\Content\Modules\ExtractorMethods;

use PHPUnit\Framework\TestCase;
use Tests\TestConfig;
use Throwable;

class ExtractInformationalImageTest extends TestCase
{
    private ExtractInformationalImageImplementation $extractor;

    protected function setUp() : void
    {
        $this->extractor = TestConfig::$di->get(ExtractInformationalImageImplementation::class);
    }

    /**
     * @throws Throwable
     */
    public function testEmptyArrayInput() : void
    {
        $payload = $this->extractor->runTest([]);

        self::assertSame('', $payload->getHeadline());
        self::assertSame('', $payload->getSubHeadline());
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
    public function testImageNotArray() : void
    {
        $payload = $this->extractor->runTest(['image' => 'BarBaz']);

        self::assertSame('', $payload->getHeadline());
        self::assertSame('', $payload->getSubHeadline());
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
    public function testNonStringValues() : void
    {
        $payload = $this->extractor->runTest([
            'headline' => 436,
            'subHeadline' => 476.34,
            'content' => ['stuff'],
        ]);

        self::assertSame('', $payload->getHeadline());
        self::assertSame('', $payload->getSubHeadline());
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
    public function testImageNoSources() : void
    {
        $payload = $this->extractor->runTest([
            'headline' => 'HeadlineValue',
            'subHeadline' => 'SubHeadlineValue',
            'content' => 'ContentValue',
            'image' => [
                '1x' => 123,
                '2x' => 576.3,
                'alt' => ['foobar'],
            ],
        ]);

        self::assertSame('HeadlineValue', $payload->getHeadline());
        self::assertSame('SubHeadlineValue', $payload->getSubHeadline());
        self::assertSame("<p>ContentValue</p>\n", $payload->getContent());

        $image = $payload->getImage();
        self::assertSame('', $image->getOneX());
        self::assertSame('', $image->getTwoX());
        self::assertSame('', $image->getAlt());
        self::assertSame([], $image->getSources());
    }

    /**
     * @throws Throwable
     */
    public function testImageWhenSourcesNotArray() : void
    {
        $payload = $this->extractor->runTest([
            'headline' => 'HeadlineValue',
            'subHeadline' => 'SubHeadlineValue',
            'content' => 'ContentValue',
            'image' => ['sources' => 'FooBar'],
        ]);

        self::assertSame('HeadlineValue', $payload->getHeadline());
        self::assertSame('SubHeadlineValue', $payload->getSubHeadline());
        self::assertSame("<p>ContentValue</p>\n", $payload->getContent());

        $image = $payload->getImage();
        self::assertSame('', $image->getOneX());
        self::assertSame('', $image->getTwoX());
        self::assertSame('', $image->getAlt());
        self::assertSame([], $image->getSources());
    }

    /**
     * @throws Throwable
     */
    public function testWithImageSources() : void
    {
        $payload = $this->extractor->runTest([
            'headline' => 'HeadlineValue',
            'subHeadline' => 'SubHeadlineValue',
            'content' => 'ContentValue',
            'image' => [
                '1x' => 'TestSrc',
                '2x' => 'TestSrcset',
                'alt' => 'TestAlt',
                'sources' => [
                    ['foo' => 'bar'],
                    [
                        '1x' => 'TestOneX',
                        '2x' => 'Test2x',
                        'mediaQuery' => 'TestMediaQuery',
                    ],
                ],
            ],
        ]);

        self::assertSame('HeadlineValue', $payload->getHeadline());
        self::assertSame('SubHeadlineValue', $payload->getSubHeadline());
        self::assertSame("<p>ContentValue</p>\n", $payload->getContent());

        $image = $payload->getImage();
        self::assertSame('TestSrc', $image->getOneX());
        self::assertSame('TestSrcset', $image->getTwoX());
        self::assertSame('TestAlt', $image->getAlt());

        $sources = $image->getSources();
        self::assertCount(2, $sources);

        $source0 = $sources[0];
        self::assertSame('', $source0->getOneX());
        self::assertSame('', $source0->getTwoX());
        self::assertSame('', $source0->getMediaQuery());

        $source1 = $sources[1];
        self::assertSame('TestOneX', $source1->getOneX());
        self::assertSame('Test2x', $source1->getTwoX());
        self::assertSame('TestMediaQuery', $source1->getMediaQuery());
    }
}
