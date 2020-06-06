<?php

declare(strict_types=1);

namespace Tests\Content\Modules\ExtractorMethods;

use PHPUnit\Framework\TestCase;
use Throwable;

class ExtractShowCaseTest extends TestCase
{
    private ExtractShowCaseImplementation $extractor;

    protected function setUp() : void
    {
        $this->extractor = new ExtractShowCaseImplementation();
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
        self::assertSame([], $payload->getCtas());

        $showCaseImage = $payload->getShowCaseImage();
        self::assertSame('', $showCaseImage->getOneX());
        self::assertSame('', $showCaseImage->getTwoX());
        self::assertSame('', $showCaseImage->getAlt());
        self::assertSame([], $showCaseImage->getSources());
    }

    /**
     * @throws Throwable
     */
    public function testCtasAndShowCaseImageNotArray() : void
    {
        $payload = $this->extractor->runTest([
            'ctas' => 'FooBar',
            'showCaseImage' => 'BarBaz',
        ]);

        self::assertSame('', $payload->getHeadline());
        self::assertSame('', $payload->getSubHeadline());
        self::assertSame('', $payload->getContent());
        self::assertSame([], $payload->getCtas());

        $showCaseImage = $payload->getShowCaseImage();
        self::assertSame('', $showCaseImage->getOneX());
        self::assertSame('', $showCaseImage->getTwoX());
        self::assertSame('', $showCaseImage->getAlt());
        self::assertSame([], $showCaseImage->getSources());
    }

    /**
     * @throws Throwable
     */
    public function testNonStringValues() : void
    {
        $payload = $this->extractor->runTest([
            'headline' => 356.23,
            'subHeadline' => true,
            'content' => 234,
        ]);

        self::assertSame('356.23', $payload->getHeadline());
        self::assertSame('1', $payload->getSubHeadline());
        self::assertSame('234', $payload->getContent());
        self::assertSame([], $payload->getCtas());

        $showCaseImage = $payload->getShowCaseImage();
        self::assertSame('', $showCaseImage->getOneX());
        self::assertSame('', $showCaseImage->getTwoX());
        self::assertSame('', $showCaseImage->getAlt());
        self::assertSame([], $showCaseImage->getSources());
    }

    /**
     * @throws Throwable
     */
    public function testShowCaseImageNoSources() : void
    {
        $payload = $this->extractor->runTest([
            'headline' => 'TestHeadline',
            'subHeadline' => 'TestSubHeadline',
            'content' => 'TestPreHeadline',
            'showCaseImage' => [
                '1x' => 'TestShowCaseSrc',
                '2x' => 'TestShowCaseSrcset',
                'alt' => 'TestShowCaseAlt',
            ],
        ]);

        self::assertSame('TestHeadline', $payload->getHeadline());
        self::assertSame('TestSubHeadline', $payload->getSubHeadline());
        self::assertSame('TestPreHeadline', $payload->getContent());
        self::assertSame([], $payload->getCtas());

        $showCaseImage = $payload->getShowCaseImage();
        self::assertSame('TestShowCaseSrc', $showCaseImage->getOneX());
        self::assertSame('TestShowCaseSrcset', $showCaseImage->getTwoX());
        self::assertSame('TestShowCaseAlt', $showCaseImage->getAlt());
        self::assertSame([], $showCaseImage->getSources());
    }

    /**
     * @throws Throwable
     */
    public function testShowCaseImageWhenSourcesNotArray() : void
    {
        $payload = $this->extractor->runTest([
            'headline' => 'TestHeadline',
            'subHeadline' => 'TestSubHeadline',
            'content' => 'TestPreHeadline',
            'showCaseImage' => [
                '1x' => 'TestShowCaseSrc',
                '2x' => 'TestShowCaseSrcset',
                'alt' => 'TestShowCaseAlt',
                'sources' => 'FooBar',
            ],
        ]);

        self::assertSame('TestHeadline', $payload->getHeadline());
        self::assertSame('TestSubHeadline', $payload->getSubHeadline());
        self::assertSame('TestPreHeadline', $payload->getContent());
        self::assertSame([], $payload->getCtas());

        $showCaseImage = $payload->getShowCaseImage();
        self::assertSame('TestShowCaseSrc', $showCaseImage->getOneX());
        self::assertSame('TestShowCaseSrcset', $showCaseImage->getTwoX());
        self::assertSame('TestShowCaseAlt', $showCaseImage->getAlt());
        self::assertSame([], $showCaseImage->getSources());
    }

    /**
     * @throws Throwable
     */
    public function testWithShowCaseImageSources() : void
    {
        $payload = $this->extractor->runTest([
            'headline' => 'TestHeadline',
            'subHeadline' => 'TestSubHeadline',
            'content' => 'TestPreHeadline',
            'showCaseImage' => [
                '1x' => 'TestShowCaseSrc',
                '2x' => 'TestShowCaseSrcset',
                'alt' => 'TestShowCaseAlt',
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

        self::assertSame('TestHeadline', $payload->getHeadline());
        self::assertSame('TestSubHeadline', $payload->getSubHeadline());
        self::assertSame('TestPreHeadline', $payload->getContent());
        self::assertSame([], $payload->getCtas());

        $showCaseImage = $payload->getShowCaseImage();
        self::assertSame('TestShowCaseSrc', $showCaseImage->getOneX());
        self::assertSame('TestShowCaseSrcset', $showCaseImage->getTwoX());
        self::assertSame('TestShowCaseAlt', $showCaseImage->getAlt());

        $sources = $showCaseImage->getSources();
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

    /**
     * @throws Throwable
     */
    public function testCtas() : void
    {
        $payload = $this->extractor->runTest([
            'ctas' => [
                ['foo' => 'bar'],
                [
                    'href' => 'TestHref',
                    'content' => 'TestContent',
                ],
            ],
        ]);

        self::assertSame('', $payload->getHeadline());
        self::assertSame('', $payload->getSubHeadline());
        self::assertSame('', $payload->getContent());

        $showCaseImage = $payload->getShowCaseImage();
        self::assertSame('', $showCaseImage->getOneX());
        self::assertSame('', $showCaseImage->getTwoX());
        self::assertSame('', $showCaseImage->getAlt());
        self::assertSame([], $showCaseImage->getSources());

        $ctas = $payload->getCtas();
        self::assertCount(2, $ctas);

        $cta0 = $ctas[0];
        self::assertSame('', $cta0->getHref());
        self::assertSame('', $cta0->getContent());

        $cta1 = $ctas[1];
        self::assertSame('TestHref', $cta1->getHref());
        self::assertSame('TestContent', $cta1->getContent());
    }
}
