<?php

declare(strict_types=1);

namespace Tests\Content\Modules;

use App\Content\Modules\ExtractModulesFromPath;
use cebe\markdown\GithubMarkdown;
use Error;
use PHPUnit\Framework\TestCase;
use Tests\TestConfig;
use Throwable;
use UnexpectedValueException;
use function assert;

class ExtractModulesFromPathTest extends TestCase
{
    private ExtractModulesFromPath $extractModulesFromPath;

    protected function setUp() : void
    {
        $this->extractModulesFromPath = new ExtractorImplementation(
            __DIR__ . '/TestContent',
            TestConfig::$di->get(GithubMarkdown::class)
        );
    }

    /**
     * @throws Throwable
     */
    public function testWhenModulesDireDoesntExist() : void
    {
        self::expectException(UnexpectedValueException::class);

        ($this->extractModulesFromPath)('NoModulesDir');
    }

    /**
     * @throws Throwable
     */
    public function testWhenContentDirectoryIsEmpty() : void
    {
        $payload = ($this->extractModulesFromPath)('TestEmpty');

        self::assertSame([], $payload->getItems());
    }

    /**
     * @throws Throwable
     */
    public function testWhenMethodDoesNotExist() : void
    {
        self::expectException(Error::class);

        self::expectExceptionMessage(
            'Call to undefined method ' . ExtractorImplementation::class . '::extractAsdf()'
        );

        ($this->extractModulesFromPath)('Asdf');
    }

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $payload = ($this->extractModulesFromPath)('TestImplementation');

        $items = $payload->getItems();

        self::assertCount(1, $items);

        $item = $items[0];
        assert($item instanceof ExtractorImplementationPayload);

        self::assertSame(
            [
                'type' => 'TestContent',
                'foo' => 'bar',
                'bar' => 'Baz',
            ],
            $item->getYaml()
        );
    }
}
