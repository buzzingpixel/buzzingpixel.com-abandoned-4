<?php

declare(strict_types=1);

namespace Tests\App\Content\Modules\ExtractorMethods;

use PHPUnit\Framework\TestCase;
use Tests\TestConfig;
use Throwable;

class ExtractCtasTest extends TestCase
{
    /** @var ExtractCtasImplementation */
    private $extractor;

    protected function setUp() : void
    {
        $this->extractor = TestConfig::$di->get(ExtractCtasImplementation::class);
    }

    /**
     * @throws Throwable
     */
    public function testWithEmptyArrayInput() : void
    {
        $payload = $this->extractor->runTest([]);

        self::assertSame([], $payload->getCtas());
    }

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $payload = $this->extractor->runTest([
            'ctas' => [
                [
                    'foo' => 'bar',
                    'bar' => 'baz',
                ],
                [
                    'href' => 'TestHref',
                    'content' => 'TestContent',
                ],
            ],
        ]);

        $ctas = $payload->getCtas();
        self::assertCount(2, $ctas);

        $cta1 = $ctas[0];
        self::assertSame('', $cta1->getHref());
        self::assertSame('', $cta1->getContent());

        $cta2 = $ctas[1];
        self::assertSame('TestHref', $cta2->getHref());
        self::assertSame('TestContent', $cta2->getContent());
    }
}
