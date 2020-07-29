<?php

declare(strict_types=1);

namespace Tests\Content\Modules\ExtractorMethods;

use PHPUnit\Framework\TestCase;
use Tests\TestConfig;
use Throwable;

class ExtractSimpleTextInterrupterTest extends TestCase
{
    private ExtractSimpleTextInterrupterImplementation $extractor;

    protected function setUp(): void
    {
        $this->extractor = TestConfig::$di->get(
            ExtractSimpleTextInterrupterImplementation::class
        );
    }

    /**
     * @throws Throwable
     */
    public function testEmptyArrayInput(): void
    {
        $payload = $this->extractor->runTest([]);

        self::assertFalse($payload->getIsH1());
        self::assertSame('', $payload->getBackgroundColor());
        self::assertSame('', $payload->getTextColor());
        self::assertSame('', $payload->getHeadline());
        self::assertSame('', $payload->getSubHeadline());
    }

    /**
     * @throws Throwable
     */
    public function test(): void
    {
        $payload = $this->extractor->runTest([
            'isH1' => true,
            'backgroundColor' => 'foo',
            'textColor' => 'bar',
            'headline' => 'baz',
            'subHeadline' => 'barf',
        ]);

        self::assertTrue($payload->getIsH1());
        self::assertSame('foo', $payload->getBackgroundColor());
        self::assertSame('bar', $payload->getTextColor());
        self::assertSame('baz', $payload->getHeadline());
        self::assertSame('barf', $payload->getSubHeadline());
    }
}
