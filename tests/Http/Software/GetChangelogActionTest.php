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
use App\Http\Software\GetChangelogAction;
use App\Http\Software\GetChangelogResponder;
use App\Http\Software\PathMap;
use App\HttpHelpers\Pagination\Pagination;
use App\HttpHelpers\Segments\ExtractUriSegments;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use ReflectionException;
use Slim\Exception\HttpNotFoundException;
use Tests\TestConfig;
use Throwable;
use function func_get_args;

class GetChangelogActionTest extends TestCase
{
    /** @var GetChangelogAction */
    private $action;

    /** @var int */
    private $pageNum = 1;
    /** @var string */
    private $softwareInfoChangelogExternalUrl = '';
    /** @var int */
    private $releasePayloadCount = 0;
    /** @var bool */
    private $noResponderExpectation = false;

    /** @var SoftwareInfoPayload */
    private $softwareInfoPayload;
    /** @var MetaPayload */
    private $metaPayload;

    /** @var MockObject&ResponseInterface */
    private $response;
    /** @var GetChangelogResponder&MockObject */
    private $getChangelogResponder;
    /** @var ExtractSoftwareInfoFromPath&MockObject */
    private $extractSoftwareInfoFromPath;
    /** @var ParseChangelogFromMarkdownFile&MockObject */
    private $parseChangelog;
    /** @var ExtractMetaFromPath&MockObject */
    private $extractMetaFromPath;
    /** @var MockObject&ServerRequestInterface */
    private $request;

    /** @var mixed[] */
    private $responderInvokeArgs;

    /**
     * @throws Throwable
     */
    public function testPage1() : void
    {
        $this->pageNum                          = 1;
        $this->softwareInfoChangelogExternalUrl = '';
        $this->releasePayloadCount              = 2;
        $this->noResponderExpectation           = false;

        $this->internalSetup();

        $response = ($this->action)($this->request);

        self::assertSame($this->response, $response);

        self::assertCount(6, $this->responderInvokeArgs);

        /** @var MetaPayload|null $metaPayload */
        $metaPayload = $this->responderInvokeArgs[0];
        self::assertInstanceOf(MetaPayload::class, $metaPayload);
        self::assertSame('Changelog | Ansel', $metaPayload->getMetaTitle());
        self::assertSame('', $metaPayload->getMetaDescription());
        self::assertSame('BarBaz', $metaPayload->getOgType());

        /** @var ChangelogPayload $allChangelogPayload */
        $allChangelogPayload = $this->responderInvokeArgs[1];
        self::assertCount(2, $allChangelogPayload->getReleases());

        /** @var ChangelogPayload $changelogPayload */
        $changelogPayload = $this->responderInvokeArgs[2];
        self::assertCount(2, $changelogPayload->getReleases());
        self::assertNotSame($allChangelogPayload, $changelogPayload);

        /** @var Pagination $pagination */
        $pagination = $this->responderInvokeArgs[3];
        self::assertSame('/software/ansel-craft/changelog', $pagination->base());
        self::assertSame(2, $pagination->totalResults());
        self::assertSame(10, $pagination->perPage());
        self::assertSame(1, $pagination->totalPages());
        self::assertSame(1, $pagination->currentPage());

        /** @var SoftwareInfoPayload $softwareInfoPayload */
        $softwareInfoPayload = $this->responderInvokeArgs[4];
        self::assertSame($this->softwareInfoPayload, $softwareInfoPayload);

        /** @var string $uriPath */
        $uriPath = $this->responderInvokeArgs[5];
        self::assertSame('/software/ansel-craft', $uriPath);
    }

    /**
     * @throws Throwable
     */
    public function testPage2() : void
    {
        $this->pageNum                          = 2;
        $this->softwareInfoChangelogExternalUrl = '/foo/bar';
        $this->releasePayloadCount              = 14;
        $this->noResponderExpectation           = false;

        $this->internalSetup();

        $response = ($this->action)($this->request);

        self::assertSame($this->response, $response);

        self::assertCount(6, $this->responderInvokeArgs);

        /** @var MetaPayload|null $metaPayload */
        $metaPayload = $this->responderInvokeArgs[0];
        self::assertInstanceOf(MetaPayload::class, $metaPayload);
        self::assertSame('Page 2 | Changelog | Ansel', $metaPayload->getMetaTitle());
        self::assertSame('', $metaPayload->getMetaDescription());
        self::assertSame('BarBaz', $metaPayload->getOgType());

        /** @var ChangelogPayload $allChangelogPayload */
        $allChangelogPayload = $this->responderInvokeArgs[1];
        self::assertCount(14, $allChangelogPayload->getReleases());

        /** @var ChangelogPayload $changelogPayload */
        $changelogPayload = $this->responderInvokeArgs[2];
        self::assertCount(4, $changelogPayload->getReleases());
        self::assertNotSame($allChangelogPayload, $changelogPayload);

        /** @var Pagination $pagination */
        $pagination = $this->responderInvokeArgs[3];
        self::assertSame('/software/ansel-craft/changelog', $pagination->base());
        self::assertSame(14, $pagination->totalResults());
        self::assertSame(10, $pagination->perPage());
        self::assertSame(2, $pagination->totalPages());
        self::assertSame(2, $pagination->currentPage());

        /** @var SoftwareInfoPayload $softwareInfoPayload */
        $softwareInfoPayload = $this->responderInvokeArgs[4];
        self::assertSame($this->softwareInfoPayload, $softwareInfoPayload);

        /** @var string $uriPath */
        $uriPath = $this->responderInvokeArgs[5];
        self::assertSame('/software/ansel-craft', $uriPath);
    }

    /**
     * @throws Throwable
     */
    public function testPage2WhenNoPage2() : void
    {
        $this->pageNum                          = 2;
        $this->softwareInfoChangelogExternalUrl = '';
        $this->releasePayloadCount              = 2;
        $this->noResponderExpectation           = true;

        $this->internalSetup();

        $exception = null;

        try {
            ($this->action)($this->request);
        } catch (HttpNotFoundException $e) {
            self::assertSame($this->request, $e->getRequest());

            $exception = $e;
        }

        self::assertInstanceOf(HttpNotFoundException::class, $exception);
    }

    /**
     * @throws Throwable
     */
    protected function internalSetup() : void
    {
        $this->mockResponse();
        $this->mockExtractSoftwareInfoFromPath();
        $this->mockParseChangelog();
        $this->mockExtractMetaFromPath();
        $this->mockRequest();
        $this->mockGetChangelogResponder();

        $this->action = new GetChangelogAction(
            $this->getChangelogResponder,
            TestConfig::$di->get(ExtractUriSegments::class),
            $this->extractSoftwareInfoFromPath,
            $this->parseChangelog,
            $this->extractMetaFromPath
        );
    }

    private function mockResponse() : void
    {
        $this->response = $this->createMock(
            ResponseInterface::class
        );
    }

    /**
     * @throws ReflectionException
     */
    private function mockExtractSoftwareInfoFromPath() : void
    {
        $this->softwareInfoPayload = new SoftwareInfoPayload([
            'changelogExternalUrl' => $this->softwareInfoChangelogExternalUrl,
        ]);

        $this->extractSoftwareInfoFromPath = $this->createMock(
            ExtractSoftwareInfoFromPath::class
        );

        $this->extractSoftwareInfoFromPath->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo(PathMap::PATH_MAP['/software/ansel-craft']))
            ->willReturn($this->softwareInfoPayload);
    }

    /**
     * @throws ReflectionException
     */
    private function mockParseChangelog() : void
    {
        $this->parseChangelog = $this->createMock(
            ParseChangelogFromMarkdownFile::class
        );

        $releases = [];

        for ($i = 0; $i < $this->releasePayloadCount; $i++) {
            $releases[] = $this->createMock(Release::class);
        }

        $payload = new ChangelogPayload(['releases' => $releases]);

        $this->parseChangelog->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo(
                $this->softwareInfoChangelogExternalUrl === '' ?
                    PathMap::PATH_MAP['/software/ansel-craft'] . '/changelog.md' :
                    $this->softwareInfoChangelogExternalUrl
            ))
            ->willReturn($payload);
    }

    /**
     * @throws Throwable
     */
    private function mockExtractMetaFromPath() : void
    {
        $this->metaPayload = new MetaPayload([
            'metaTitle' => 'Ansel',
            'metaDescription' => 'FooBar',
            'ogType' => 'BarBaz',
        ]);

        $this->extractMetaFromPath = $this->createMock(
            ExtractMetaFromPath::class
        );

        if ($this->noResponderExpectation === true) {
            return;
        }

        $this->extractMetaFromPath->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo(PathMap::PATH_MAP['/software/ansel-craft']))
            ->willReturn($this->metaPayload);
    }

    private function mockRequest() : void
    {
        $this->request = $this->createMock(
            ServerRequestInterface::class
        );

        $uri = $this->createMock(UriInterface::class);

        $uri->method('getPath')->willReturn(
            $this->pageNum > 1 ?
                '/software/ansel-craft/changelog/page/' . $this->pageNum :
                '/software/ansel-craft/changelog'
        );

        $this->request->method('getUri')->willReturn($uri);
    }

    private function mockGetChangelogResponder() : void
    {
        $this->responderInvokeArgs = [];

        $this->getChangelogResponder = $this->createMock(
            GetChangelogResponder::class
        );

        if ($this->noResponderExpectation === true) {
            return;
        }

        $this->getChangelogResponder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(function () {
                $this->responderInvokeArgs = func_get_args();

                return $this->response;
            });
    }
}
