<?php

declare(strict_types=1);

namespace Tests\App\Content\Modules\ExtractorMethods;

use PHPUnit\Framework\TestCase;
use Throwable;

class ExtractShowCaseTest extends TestCase
{
    /** @var ExtractShowCaseImplementation */
    private $extractor;

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

        self::assertSame('', $payload->getPreHeadline());
        self::assertSame('', $payload->getHeadline());
        self::assertSame('', $payload->getSubHeadline());
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

        self::assertSame('', $payload->getPreHeadline());
        self::assertSame('', $payload->getHeadline());
        self::assertSame('', $payload->getSubHeadline());
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
            'preHeadline' => 234,
            'headline' => 356.23,
            'subHeadline' => true,
        ]);

        self::assertSame('234', $payload->getPreHeadline());
        self::assertSame('356.23', $payload->getHeadline());
        self::assertSame('1', $payload->getSubHeadline());
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
            'preHeadline' => 'TestPreHeadline',
            'headline' => 'TestHeadline',
            'subHeadline' => 'TestSubHeadline',
            'showCaseImage' => [
                '1x' => 'TestShowCaseSrc',
                '2x' => 'TestShowCaseSrcset',
                'alt' => 'TestShowCaseAlt',
            ],
        ]);

        self::assertSame('TestPreHeadline', $payload->getPreHeadline());
        self::assertSame('TestHeadline', $payload->getHeadline());
        self::assertSame('TestSubHeadline', $payload->getSubHeadline());
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
            'preHeadline' => 'TestPreHeadline',
            'headline' => 'TestHeadline',
            'subHeadline' => 'TestSubHeadline',
            'showCaseImage' => [
                '1x' => 'TestShowCaseSrc',
                '2x' => 'TestShowCaseSrcset',
                'alt' => 'TestShowCaseAlt',
                'sources' => 'FooBar',
            ],
        ]);

        self::assertSame('TestPreHeadline', $payload->getPreHeadline());
        self::assertSame('TestHeadline', $payload->getHeadline());
        self::assertSame('TestSubHeadline', $payload->getSubHeadline());
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
            'preHeadline' => 'TestPreHeadline',
            'headline' => 'TestHeadline',
            'subHeadline' => 'TestSubHeadline',
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

        self::assertSame('TestPreHeadline', $payload->getPreHeadline());
        self::assertSame('TestHeadline', $payload->getHeadline());
        self::assertSame('TestSubHeadline', $payload->getSubHeadline());
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

        self::assertSame('', $payload->getPreHeadline());
        self::assertSame('', $payload->getHeadline());
        self::assertSame('', $payload->getSubHeadline());

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
