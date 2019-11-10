<?php

declare(strict_types=1);

namespace Tests\App\Content\Modules\ExtractorMethods;

use PHPUnit\Framework\TestCase;
use Throwable;

class ExtractQuoteBlocksTest extends TestCase
{
    /** @var ExtractQuoteBlocksImplementation */
    private $extractor;

    protected function setUp() : void
    {
        $this->extractor = new ExtractQuoteBlocksImplementation();
    }

    /**
     * @throws Throwable
     */
    public function testEmptyArrayInput() : void
    {
        $payload = $this->extractor->runTest([]);

        self::assertSame([], $payload->getQuoteBlocks());
    }

    /**
     * @throws Throwable
     */
    public function testBlocksNotArray() : void
    {
        $payload = $this->extractor->runTest(['blocks' => 'foo']);

        self::assertSame([], $payload->getQuoteBlocks());
    }

    /**
     * @throws Throwable
     */
    public function testImageNotArray() : void
    {
        $payload = $this->extractor->runTest([
            'blocks' => [
                [
                    'name' => 'FooBar',
                    'image' => 'foobar',
                ],
                [
                    'name' => 'BarBaz',
                    'image' => ['foo' => 'bar'],
                ],
                [
                    'name' => 'BazFoo',
                    'image' => ['1x' => 'baz'],
                ],
            ],
        ]);

        $blocks = $payload->getQuoteBlocks();
        self::assertCount(3, $blocks);

        $block1 = $blocks[0];
        self::assertSame('FooBar', $block1->getPersonName());
        self::assertSame('', $block1->getPersonNameHref());
        self::assertSame('', $block1->getPosition());
        self::assertSame('', $block1->getPositionHref());
        self::assertSame('', $block1->getContent());
        $image1 = $block1->getImage();
        self::assertSame('', $image1->getOneX());
        self::assertSame('', $image1->getTwoX());
        self::assertSame('', $image1->getAlt());
        self::assertSame([], $image1->getSources());

        $block2 = $blocks[1];
        self::assertSame('BarBaz', $block2->getPersonName());
        self::assertSame('', $block2->getPersonNameHref());
        self::assertSame('', $block2->getPosition());
        self::assertSame('', $block2->getPositionHref());
        self::assertSame('', $block2->getContent());
        $image2 = $block2->getImage();
        self::assertSame('', $image2->getOneX());
        self::assertSame('', $image2->getTwoX());
        self::assertSame('', $image2->getAlt());
        self::assertSame([], $image1->getSources());

        $block3 = $blocks[2];
        self::assertSame('BazFoo', $block3->getPersonName());
        self::assertSame('', $block3->getPersonNameHref());
        self::assertSame('', $block3->getPosition());
        self::assertSame('', $block3->getPositionHref());
        self::assertSame('', $block3->getContent());
        $image3 = $block3->getImage();
        self::assertSame('baz', $image3->getOneX());
        self::assertSame('', $image3->getTwoX());
        self::assertSame('', $image3->getAlt());
        self::assertSame([], $image3->getSources());
    }

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $payload = $this->extractor->runTest([
            'blocks' => [
                [
                    'image' => [
                        '1x' => 'OneXTest',
                        '2x' => 'TwoXTest',
                        'alt' => 'TestAlt',
                    ],
                    'name' => 'TJ Draper',
                    'position' => 'Senior Developer',
                    'positionHref' => 'https://buzzingpixel.com',
                    'content' => 'Test Content',
                ],
                [
                    'image' => [
                        '1x' => 'OneXTest2',
                        '2x' => 'TwoXTest2',
                        'alt' => 'TestAlt2',
                    ],
                    'name' => 'Rachel Draper',
                    'nameHref' => 'https://test.com',
                    'position' => 'Tutor',
                    'content' => 'Testing Content',
                ],
            ],
        ]);

        $blocks = $payload->getQuoteBlocks();
        self::assertCount(2, $blocks);

        $block1 = $blocks[0];
        self::assertSame('TJ Draper', $block1->getPersonName());
        self::assertSame('', $block1->getPersonNameHref());
        self::assertSame('Senior Developer', $block1->getPosition());
        self::assertSame('https://buzzingpixel.com', $block1->getPositionHref());
        self::assertSame('Test Content', $block1->getContent());
        $image1 = $block1->getImage();
        self::assertSame('OneXTest', $image1->getOneX());
        self::assertSame('TwoXTest', $image1->getTwoX());
        self::assertSame('TestAlt', $image1->getAlt());
        self::assertSame([], $image1->getSources());

        $block2 = $blocks[1];
        self::assertSame('Rachel Draper', $block2->getPersonName());
        self::assertSame('https://test.com', $block2->getPersonNameHref());
        self::assertSame('Tutor', $block2->getPosition());
        self::assertSame('', $block2->getPositionHref());
        self::assertSame('Testing Content', $block2->getContent());
        $image2 = $block2->getImage();
        self::assertSame('OneXTest2', $image2->getOneX());
        self::assertSame('TwoXTest2', $image2->getTwoX());
        self::assertSame('TestAlt2', $image2->getAlt());
        self::assertSame([], $image1->getSources());
    }
}
