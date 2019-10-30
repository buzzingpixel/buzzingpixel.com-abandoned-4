<?php

declare(strict_types=1);

namespace Tests\App\Content\Meta;

use App\Content\Meta\ExtractMetaFromPath;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;
use Throwable;

class ExtractMetaFromPathTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testWhenDirDoesNotExist() : void
    {
        self::expectException(ParseException::class);

        self::expectExceptionMessage(
            'File "' . __DIR__ . '/asdf/meta.yml" does not exist.'
        );

        $extractMetaFromPath = new ExtractMetaFromPath(__DIR__ . '');

        $extractMetaFromPath('asdf');
    }

    /**
     * @throws Throwable
     */
    public function testWhenEmptyYamlFile() : void
    {
        $extractMetaFromPath = new ExtractMetaFromPath(__DIR__ . '');

        $payload = $extractMetaFromPath('EmptyYamlFile');

        self::assertFalse($payload->getNoIndex());
        self::assertSame('', $payload->getMetaTitle());
        self::assertSame('', $payload->getMetaDescription());
        self::assertSame('website', $payload->getOgType());
        self::assertSame('summary', $payload->getTwitterCardType());
    }

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $extractMetaFromPath = new ExtractMetaFromPath(__DIR__ . '');

        $payload = $extractMetaFromPath('TestYamlFile');

        self::assertTrue($payload->getNoIndex());
        self::assertSame('Test Meta Title', $payload->getMetaTitle());
        self::assertSame('Test Meta Description', $payload->getMetaDescription());
        self::assertSame('Test Og Type', $payload->getOgType());
        self::assertSame('Test Twitter Card Type', $payload->getTwitterCardType());
    }
}
