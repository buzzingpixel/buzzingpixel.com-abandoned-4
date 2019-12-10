<?php

declare(strict_types=1);

namespace Tests\Content\Modules\ExtractorMethods;

use PHPUnit\Framework\TestCase;
use Tests\TestConfig;
use Throwable;

class ExtractTextColumnsTest extends TestCase
{
    /** @var ExtractTextColumnsImplementation */
    private $extractor;

    protected function setUp() : void
    {
        $this->extractor = TestConfig::$di->get(ExtractTextColumnsImplementation::class);
    }

    /**
     * @throws Throwable
     */
    public function testWithEmptyArrayInput() : void
    {
        $payload = $this->extractor->runTest([]);

        self::assertSame('', $payload->getBackgroundColor());
        self::assertSame([], $payload->getItems());
    }

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $payload = $this->extractor->runTest([
            'backgroundColor' => 'TestBackgroundColor',
            'columns' => [
                [],
                [
                    'foo' => 'bar',
                    'bar' => 'baz',
                ],[
                    'headline' => 'TestHeadline',
                    'content' => 'TestContent',
                ],
            ],
        ]);

        self::assertSame('TestBackgroundColor', $payload->getBackgroundColor());

        $items = $payload->getItems();
        self::assertCount(3, $items);

        $item1 = $items[0];
        self::assertSame('', $item1->getHeadline());
        self::assertSame('', $item1->getContent());

        $item2 = $items[1];
        self::assertSame('', $item2->getHeadline());
        self::assertSame('', $item2->getContent());

        $item3 = $items[2];
        self::assertSame('TestHeadline', $item3->getHeadline());
        self::assertSame("<p>TestContent</p>\n", $item3->getContent());
    }
}
