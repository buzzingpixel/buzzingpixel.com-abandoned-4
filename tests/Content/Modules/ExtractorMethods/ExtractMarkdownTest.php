<?php

declare(strict_types=1);

namespace Tests\Content\Modules\ExtractorMethods;

use PHPUnit\Framework\TestCase;
use Tests\TestConfig;
use Throwable;

class ExtractMarkdownTest extends TestCase
{
    private ExtractMarkdownImplementation $extractor;

    protected function setUp(): void
    {
        $this->extractor = TestConfig::$di->get(
            ExtractMarkdownImplementation::class,
        );
    }

    /**
     * @throws Throwable
     */
    public function testEmptyArrayInput(): void
    {
        $payload = $this->extractor->runTest([]);

        self::assertSame('', $payload->getContent());
    }

    /**
     * @throws Throwable
     */
    public function test(): void
    {
        $payload = $this->extractor->runTest(['content' => 'foo']);

        self::assertSame('foo', $payload->getContent());
    }
}
