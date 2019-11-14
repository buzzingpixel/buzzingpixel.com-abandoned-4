<?php

declare(strict_types=1);

namespace Tests\App\Content\Changelog;

use App\Content\Changelog\ParseChangelogFromMarkdownFile;
use MJErwin\ParseAChangelog\Release;
use PHPUnit\Framework\TestCase;
use Throwable;
use function count;
use function Safe\file_get_contents;

class ParseChangelogFromMarkdownFileTest extends TestCase
{
    /** @var ParseChangelogFromMarkdownFile */
    private $parseChangelogFromMarkdownFile;

    /**
     * @throws Throwable
     */
    public function testInvalidPath() : void
    {
        $this->internalSetUp('/baz/foo/bar');

        $payload = ($this->parseChangelogFromMarkdownFile)('foo/bar/baz');

        self::assertSame([], $payload->getReleases());
    }

    /**
     * @throws Throwable
     */
    public function testInvalidUrl() : void
    {
        $this->internalSetUp('/baz/foo/bar');

        $payload = ($this->parseChangelogFromMarkdownFile)(
            'http://buzzingfoobarbaz.com/changelog.md'
        );

        self::assertSame([], $payload->getReleases());
    }

    /**
     * @throws Throwable
     */
    public function testInvalidUrl2() : void
    {
        $this->internalSetUp('/baz/foo/bar');

        $payload = ($this->parseChangelogFromMarkdownFile)(
            'https://buzzingpixel.com/foobar'
        );

        self::assertSame([], $payload->getReleases());
    }

    /**
     * @throws Throwable
     */
    public function testRealUrl() : void
    {
        $this->internalSetUp('/baz/foo/bar');

        $payload = ($this->parseChangelogFromMarkdownFile)(
            'https://raw.githubusercontent.com/buzzingpixel/ansel-craft/master/changelog.md'
        );

        self::assertTrue(count($payload->getReleases()) > 1);

        self::assertInstanceOf(Release::class, $payload->getReleases()[0]);
    }

    /**
     * @throws Throwable
     */
    public function testFile() : void
    {
        $this->internalSetUp(__DIR__);

        $payload = ($this->parseChangelogFromMarkdownFile)('TestChangeLog.md');

        $releases = $payload->getReleases();

        self::assertCount(2, $releases);

        $release1 = $releases[0];
        self::assertSame('2.1.4', $release1->getVersion());
        self::assertSame('2019-10-07', $release1->getDate());
        // file_put_contents(__DIR__ . '/ChangelogOneOutput.html', $release1->toHtml());
        self::assertSame(
            file_get_contents(__DIR__ . '/ChangelogOneOutput.html'),
            $release1->toHtml()
        );

        $release2 = $releases[1];
        self::assertSame('2.1.3', $release2->getVersion());
        self::assertSame('2019-08-24', $release2->getDate());
        // file_put_contents(__DIR__ . '/ChangelogTwoOutput.html', $release2->toHtml());
        self::assertSame(
            file_get_contents(__DIR__ . '/ChangelogTwoOutput.html'),
            $release2->toHtml()
        );

        $slicedPayload = $payload->withReleaseSlice(1, 1);

        $slicedReleases = $slicedPayload->getReleases();

        self::assertCount(2, $payload->getReleases());

        self::assertCount(1, $slicedReleases);

        $slicedRelease = $slicedReleases[0];
        self::assertSame('2.1.3', $slicedRelease->getVersion());
        self::assertSame('2019-08-24', $slicedRelease->getDate());
        self::assertSame(
            file_get_contents(__DIR__ . '/ChangelogTwoOutput.html'),
            $slicedRelease->toHtml()
        );
    }

    private function internalSetUp(string $pathToContentDirectory) : void
    {
        $this->parseChangelogFromMarkdownFile = new ParseChangelogFromMarkdownFile(
            $pathToContentDirectory
        );
    }
}
