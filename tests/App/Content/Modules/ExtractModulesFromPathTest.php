<?php

declare(strict_types=1);

namespace Tests\App\Content\Modules;

use App\Content\Modules\ExtractModulesFromPath;
use Error;
use PHPUnit\Framework\TestCase;
use Throwable;
use UnexpectedValueException;

class ExtractModulesFromPathTest extends TestCase
{
    /** @var ExtractModulesFromPath */
    private $extractModulesFromPath;

    protected function setUp() : void
    {
        $this->extractModulesFromPath = new ExtractorImplementation(
            __DIR__ . '/TestContent'
        );
    }

    /**
     * @throws Throwable
     */
    public function testWhenModulesDireDoesntExist() : void
    {
        self::expectException(UnexpectedValueException::class);

        self::expectExceptionMessage(
            'DirectoryIterator::__construct(' .
            __DIR__ . '/TestContent' .
            '/NoModulesDir/modules): failed to open dir: No such file or directory'
        );

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

        /** @var ExtractorImplementationPayload $item */
        $item = $items[0];

        self::assertInstanceOf(ExtractorImplementationPayload::class, $item);

        $yaml = $item->getYaml();

        self::assertSame(
            [
                'type' => 'TestContent',
                'foo' => 'bar',
                'bar' => 'Baz',
            ],
            $yaml
        );
    }
}
