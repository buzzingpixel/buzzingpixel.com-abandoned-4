<?php

declare(strict_types=1);

namespace Tests\App\Http\Software;

use App\Content\Changelog\ChangelogPayload;
use App\Content\Changelog\ParseChangelogFromMarkdownFile;
use App\Content\Meta\ExtractMetaFromPath;
use App\Content\Meta\MetaPayload;
use App\Content\Software\ExtractSoftwareInfoFromPath;
use App\Content\Software\SoftwareInfoPayload;
use App\Http\Software\GetChangelogItemAction;
use App\Http\Software\GetChangelogItemResponder;
use App\HttpHelpers\Segments\ExtractUriSegments;
use MJErwin\ParseAChangelog\Release;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slim\Exception\HttpNotFoundException;
use Tests\TestConfig;
use Throwable;

class GetChangelogItemActionTest extends TestCase
{
    /** @var GetChangelogItemAction */
    private $action;

    /** @var SoftwareInfoPayload */
    private $softwareInfoPayload;
    /** @var MetaPayload */
    private $metaPayload;

    /** @var MockObject&ResponseInterface */
    private $response;

    /** @var ChangelogPayload */
    private $changelogPayload;
    /** @var string */
    private $changelogExternalUrl = '';
    /** @var bool */
    private $responderExceptCall = false;
    /** @var Release|null */
    private $release;

    /**
     * @throws Throwable
     */
    public function testWhenNoReleasesOnPayload() : void
    {
        $this->release              = null;
        $this->changelogPayload     = new ChangelogPayload();
        $this->changelogExternalUrl = '';
        $this->responderExceptCall  = false;

        $this->internalSetUp();

        $exception = null;

        $request = $this->mockRequest();

        try {
            ($this->action)($request);
        } catch (HttpNotFoundException $e) {
            self::assertSame($request, $e->getRequest());

            $exception = $e;
        }

        self::assertInstanceOf(
            HttpNotFoundException::class,
            $exception
        );
    }

    /**
     * @throws Throwable
     */
    public function testWhenNoMatchingReleases() : void
    {
        $this->release              = null;
        $this->changelogPayload     = new ChangelogPayload([
            'releases' => [
                $this->mockRelease('2.0.0'),
                $this->mockRelease('1.9.8'),
                $this->mockRelease('1.9.3'),
                $this->mockRelease('123455'),
            ],
        ]);
        $this->changelogExternalUrl = '/test/url';
        $this->responderExceptCall  = false;

        $this->internalSetUp();

        $exception = null;

        $request = $this->mockRequest();

        try {
            ($this->action)($request);
        } catch (HttpNotFoundException $e) {
            self::assertSame($request, $e->getRequest());

            $exception = $e;
        }

        self::assertInstanceOf(
            HttpNotFoundException::class,
            $exception
        );
    }

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $this->release              = $this->mockRelease('2.1.3');
        $this->changelogPayload     = new ChangelogPayload([
            'releases' => [
                $this->mockRelease('2.0.0'),
                $this->release,
                $this->mockRelease('1.9.3'),
                $this->mockRelease('123455'),
            ],
        ]);
        $this->changelogExternalUrl = '/foo/bar';
        $this->responderExceptCall  = true;

        $this->internalSetUp();

        $response = ($this->action)($this->mockRequest());

        self::assertSame($this->response, $response);
    }

    /**
     * @throws Throwable
     */
    protected function internalSetUp() : void
    {
        $this->softwareInfoPayload = new SoftwareInfoPayload([
            'changelogExternalUrl' => $this->changelogExternalUrl,
        ]);

        $this->metaPayload = new MetaPayload();

        $this->response = $this->mockResponse();

        $this->action = new GetChangelogItemAction(
            $this->mockResponder(),
            TestConfig::$di->get(ExtractUriSegments::class),
            $this->mockExtractSoftwareInfoFromPath(),
            $this->mockParseChangelogFromMarkdownFile(),
            $this->mockExtractMetaFromPath()
        );
    }

    /**
     * @return MockObject&ResponseInterface
     */
    private function mockResponse()
    {
        return $this->createMock(
            ResponseInterface::class
        );
    }

    /**
     * @return GetChangelogItemResponder&MockObject
     */
    private function mockResponder()
    {
        $responder = $this->createMock(
            GetChangelogItemResponder::class
        );

        if (! $this->responderExceptCall) {
            return $responder;
        }

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo($this->metaPayload),
                self::equalTo($this->release),
                self::equalTo($this->softwareInfoPayload),
                self::equalTo('/software/ansel-craft')
            )
            ->willReturn($this->response);

        return $responder;
    }

    /**
     * @return ExtractSoftwareInfoFromPath&MockObject
     */
    private function mockExtractSoftwareInfoFromPath()
    {
        $extractSoftwareInfoFromPath = $this->createMock(
            ExtractSoftwareInfoFromPath::class
        );

        $extractSoftwareInfoFromPath->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo('Software/AnselCraft'))
            ->willReturn($this->softwareInfoPayload);

        return $extractSoftwareInfoFromPath;
    }

    /**
     * @return ParseChangelogFromMarkdownFile&MockObject
     */
    private function mockParseChangelogFromMarkdownFile()
    {
        $parseChangelog = $this->createMock(
            ParseChangelogFromMarkdownFile::class
        );

        if (! $this->responderExceptCall) {
            return $parseChangelog;
        }

        $parseChangelog->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo(
                $this->changelogExternalUrl === '' ?
                    'Software/AnselCraft/changelog.md' :
                    $this->changelogExternalUrl
            ))
            ->willReturn($this->changelogPayload);

        return $parseChangelog;
    }

    /**
     * @return ExtractMetaFromPath&MockObject
     */
    private function mockExtractMetaFromPath()
    {
        $extractMeta = $this->createMock(
            ExtractMetaFromPath::class
        );

        if (! $this->responderExceptCall) {
            return $extractMeta;
        }

        $extractMeta->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo('Software/AnselCraft'))
            ->willReturn($this->metaPayload);

        return $extractMeta;
    }

    /**
     * @return MockObject&ServerRequestInterface
     */
    private function mockRequest()
    {
        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->method('getUri')
            ->willReturn($this->mockUri());

        return $request;
    }

    /**
     * @return MockObject&UriInterface
     */
    private function mockUri()
    {
        $uri = $this->createMock(UriInterface::class);

        $uri->method('getPath')->willReturn(
            '/software/ansel-craft/changelog/2.1.3'
        );

        return $uri;
    }

    /**
     * @return Release&MockObject
     */
    private function mockRelease(string $version)
    {
        $release = $this->createMock(Release::class);

        $release->method('getVersion')->willReturn($version);

        return $release;
    }
}
