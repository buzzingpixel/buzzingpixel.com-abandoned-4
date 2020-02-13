<?php

declare(strict_types=1);

namespace Tests\Http\Software;

use App\Content\Changelog\ChangelogPayload;
use App\Content\Changelog\ParseChangelogFromMarkdownFile;
use App\Content\Changelog\Release;
use App\Content\Meta\ExtractMetaFromPath;
use App\Content\Meta\MetaPayload;
use App\Content\Software\ExtractSoftwareInfoFromPath;
use App\Content\Software\SoftwareInfoPayload;
use App\Http\Software\GetChangelogItemAction;
use App\Http\Software\GetChangelogItemResponder;
use App\HttpHelpers\Segments\ExtractUriSegments;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slim\Exception\HttpNotFoundException;
use Tests\TestConfig;
use Throwable;
use function func_get_args;

class GetChangelogItemActionTest extends TestCase
{
    private GetChangelogItemAction $action;

    private SoftwareInfoPayload $softwareInfoPayload;
    private MetaPayload $metaPayload;

    /** @var MockObject&ResponseInterface */
    private $response;

    private ChangelogPayload $changelogPayload;
    private string $changelogExternalUrl = '';
    private bool $responderExceptCall    = false;
    private ?Release $release;
    /** @var mixed[] */
    private array $responderCallArgs;

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

        self::assertCount(4, $this->responderCallArgs);

        /** @var MetaPayload|null $metaPayload */
        $metaPayload = $this->responderCallArgs[0];
        self::assertInstanceOf(MetaPayload::class, $metaPayload);
        self::assertSame('Version 2.1.3 | Changelog | Ansel', $metaPayload->getMetaTitle());
        self::assertSame('', $metaPayload->getMetaDescription());
        self::assertSame('BarBaz', $metaPayload->getOgType());

        /** @var Release|null $release */
        $release = $this->responderCallArgs[1];
        self::assertInstanceOf(Release::class, $release);
        self::assertSame($this->release, $release);

        /** @var SoftwareInfoPayload|null $softwareInfoPayload */
        $softwareInfoPayload = $this->responderCallArgs[2];
        self::assertInstanceOf(SoftwareInfoPayload::class, $softwareInfoPayload);
        self::assertSame($this->softwareInfoPayload, $softwareInfoPayload);

        /** @var string $uriPath */
        $uriPath = $this->responderCallArgs[3];
        self::assertSame('/software/ansel-craft', $uriPath);
    }

    /**
     * @throws Throwable
     */
    protected function internalSetUp() : void
    {
        $this->softwareInfoPayload = new SoftwareInfoPayload([
            'changelogExternalUrl' => $this->changelogExternalUrl,
        ]);

        $this->metaPayload = new MetaPayload([
            'metaTitle' => 'Ansel',
            'metaDescription' => 'FooBar',
            'ogType' => 'BarBaz',
        ]);

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
        $this->responderCallArgs = [];

        $responder = $this->createMock(
            GetChangelogItemResponder::class
        );

        if (! $this->responderExceptCall) {
            return $responder;
        }

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(function () {
                $this->responderCallArgs = func_get_args();

                return $this->response;
            });

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
