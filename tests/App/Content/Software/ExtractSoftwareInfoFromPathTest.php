<?php

declare(strict_types=1);

namespace Tests\App\Content\Software;

use App\Content\Software\ExtractSoftwareInfoFromPath;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;
use Throwable;

class ExtractSoftwareInfoFromPathTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testWhenDirDoesNotExist() : void
    {
        self::expectException(ParseException::class);

        self::expectExceptionMessage(
            'File "' . __DIR__ . '/bar/software.yml" does not exist.'
        );

        $extractSoftwareInfoFromPath = new ExtractSoftwareInfoFromPath(__DIR__);

        $extractSoftwareInfoFromPath('bar');
    }

    /**
     * @throws Throwable
     */
    public function testWhenEmptyYamlFile() : void
    {
        $extractSoftwareInfoFromPath = new ExtractSoftwareInfoFromPath(__DIR__);

        $payload = $extractSoftwareInfoFromPath('EmptyYamlFile');

        self::assertSame('', $payload->getTitle());
        self::assertSame('', $payload->getSubTitle());
        self::assertFalse($payload->getForSale());
        self::assertFalse($payload->getHasChangelog());
        self::assertSame('', $payload->getChangelogExternalUrl());
        self::assertFalse($payload->getHasDocumentation());
        self::assertSame([], $payload->getActionButtons());
    }

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $extractSoftwareInfoFromPath = new ExtractSoftwareInfoFromPath(__DIR__);

        $payload = $extractSoftwareInfoFromPath('TestYamlFile');

        self::assertSame('TestTitle', $payload->getTitle());
        self::assertSame('TestSubTitle', $payload->getSubTitle());
        self::assertTrue($payload->getForSale());
        self::assertTrue($payload->getHasChangelog());
        self::assertSame('/test/changelog/url', $payload->getChangelogExternalUrl());
        self::assertTrue($payload->getHasDocumentation());

        $actionButtons = $payload->getActionButtons();
        self::assertCount(3, $actionButtons);

        $actionButton0 = $actionButtons[0];
        self::assertSame('href1test', $actionButton0->getHref());
        self::assertSame('', $actionButton0->getContent());

        $actionButton1 = $actionButtons[1];
        self::assertSame('', $actionButton1->getHref());
        self::assertSame('content2test', $actionButton1->getContent());

        $actionButton2 = $actionButtons[2];
        self::assertSame('test3href', $actionButton2->getHref());
        self::assertSame('content3test', $actionButton2->getContent());
    }
}
