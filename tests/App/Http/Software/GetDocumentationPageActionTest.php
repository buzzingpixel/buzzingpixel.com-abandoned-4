<?php

declare(strict_types=1);

namespace Tests\App\Http\Software;

use App\Content\Documentation\CollectDocumentationVersionsFromPath;
use App\Content\Documentation\DocumentationPagePayload;
use App\Content\Documentation\DocumentationVersionPayload;
use App\Content\Documentation\DocumentationVersionsPayload;
use App\Content\Meta\ExtractMetaFromPath;
use App\Content\Meta\MetaPayload;
use App\Http\Software\GetDocumentationPageAction;
use App\Http\Software\GetDocumentationPageResponder;
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

class GetDocumentationPageActionTest extends TestCase
{
    /** @var GetDocumentationPageAction */
    private $action;

    /** @var DocumentationPagePayload&MockObject */
    private $activePage;
    /** @var DocumentationVersionPayload&MockObject */
    private $activeVersion;
    /** @var DocumentationVersionsPayload */
    private $documentationVersionsPayload;
    /** @var MetaPayload */
    private $metaPayload;

    /** @var MockObject&ResponseInterface */
    private $response;
    /** @var MockObject&ServerRequestInterface */
    private $request;

    /** @var mixed[]|null */
    private $responderArgs;

    /** @var bool */
    private $expectResponderInvoke = false;
    /** @var bool */
    private $versionsShouldReturnActiveVersion = false;
    /** @var bool */
    private $activeVersionShouldReturnPage = false;

    /**
     * @throws Throwable
     */
    public function testWhenNoActiveVersion() : void
    {
        $this->expectResponderInvoke             = false;
        $this->versionsShouldReturnActiveVersion = false;
        $this->activeVersionShouldReturnPage     = false;

        $this->internalSetUp();

        $exception = null;

        try {
            ($this->action)($this->request);
        } catch (HttpNotFoundException $e) {
            self::assertSame($this->request, $e->getRequest());

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
    public function testWhenNoActivePage() : void
    {
        $this->expectResponderInvoke             = false;
        $this->versionsShouldReturnActiveVersion = true;
        $this->activeVersionShouldReturnPage     = false;

        $this->internalSetUp();

        $exception = null;

        try {
            ($this->action)($this->request);
        } catch (HttpNotFoundException $e) {
            self::assertSame($this->request, $e->getRequest());

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
        $this->expectResponderInvoke             = true;
        $this->versionsShouldReturnActiveVersion = true;
        $this->activeVersionShouldReturnPage     = true;

        $this->internalSetUp();

        $response = ($this->action)($this->request);

        self::assertSame(
            $this->response,
            $response
        );

        /** @var array $args */
        $args = $this->responderArgs;
        self::assertCount(5, $args);

        /** @var string $arg1 */
        $arg1 = $args[0];
        self::assertSame('/software/ansel-craft', $arg1);

        /** @var MetaPayload|null $arg2 */
        $arg2 = $args[1];
        self::assertInstanceOf(MetaPayload::class, $arg2);
        self::assertSame(
            'Test Active Title | Documentation | Test Meta Title',
            $arg2->getMetaTitle()
        );
        self::assertSame('', $arg2->getMetaDescription());

        /** @var DocumentationPagePayload $arg3 */
        $arg3 = $args[2];
        self::assertSame($this->activePage, $arg3);

        /** @var DocumentationVersionPayload $arg4 */
        $arg4 = $args[3];
        self::assertSame($this->activeVersion, $arg4);

        /** @var DocumentationVersionsPayload $arg5 */
        $arg5 = $args[4];
        self::assertSame(
            $this->documentationVersionsPayload,
            $arg5
        );
    }

    /**
     * @throws Throwable
     */
    protected function internalSetUp() : void
    {
        $this->activePage = $this->mockActivePage();

        $this->activeVersion = $this->mockActiveVersion();

        $this->documentationVersionsPayload = $this->mockDocumentationVersionsPayload();

        $this->metaPayload = new MetaPayload([
            'metaTitle' => 'Test Meta Title',
            'metaDescription' => 'Test Meta Description',
        ]);

        $this->response = $this->mockResponse();

        $this->request = $this->mockRequest();

        $this->action = new GetDocumentationPageAction(
            $this->mockResponder(),
            TestConfig::$di->get(ExtractUriSegments::class),
            $this->mockDocumentationVersionsCollector(),
            $this->mockExtractMetaFromPath()
        );
    }

    /**
     * @return DocumentationPagePayload&MockObject
     */
    private function mockActivePage()
    {
        $activePage = $this->createMock(DocumentationPagePayload::class);

        $activePage->method('getTitle')
            ->willReturn('Test Active Title');

        return $activePage;
    }

    /**
     * @return DocumentationVersionPayload&MockObject
     */
    private function mockActiveVersion()
    {
        $payload = $this->createMock(
            DocumentationVersionPayload::class
        );

        $activePage = null;

        if ($this->activeVersionShouldReturnPage) {
            $activePage = $this->activePage;
        }

        $payload->method('getPageBySlug')
            ->with(self::equalTo('getting-started'))
            ->willReturn($activePage);

        return $payload;
    }

    /**
     * @return DocumentationVersionsPayload&MockObject
     */
    private function mockDocumentationVersionsPayload()
    {
        $payload = $this->createMock(DocumentationVersionsPayload::class);

        $activeVersion = null;

        if ($this->versionsShouldReturnActiveVersion) {
            $activeVersion = $this->activeVersion;
        }

        $payload->expects(self::once())
            ->method('getVersionBySlug')
            ->with(self::equalTo('v3'))
            ->willReturn($activeVersion);

        return $payload;
    }

    /**
     * @return MockObject&ResponseInterface
     */
    private function mockResponse()
    {
        return $this->createMock(ResponseInterface::class);
    }

    /**
     * @return GetDocumentationPageResponder&MockObject
     */
    private function mockResponder()
    {
        $this->responderArgs = null;

        $responder = $this->createMock(
            GetDocumentationPageResponder::class
        );

        if (! $this->expectResponderInvoke) {
            return $responder;
        }

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(function () {
                $this->responderArgs = func_get_args();

                return $this->response;
            });

        return $responder;
    }

    /**
     * @return CollectDocumentationVersionsFromPath&MockObject
     */
    private function mockDocumentationVersionsCollector()
    {
        $collector = $this->createMock(
            CollectDocumentationVersionsFromPath::class
        );

        $collector->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo('Software/AnselCraft'))
            ->willReturn($this->documentationVersionsPayload);

        return $collector;
    }

    /**
     * @return ExtractMetaFromPath&MockObject
     */
    private function mockExtractMetaFromPath()
    {
        $extractor = $this->createMock(
            ExtractMetaFromPath::class
        );

        if (! $this->expectResponderInvoke) {
            return $extractor;
        }

        $extractor->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo('Software/AnselCraft'))
            ->willReturn($this->metaPayload);

        return $extractor;
    }

    /**
     * @return MockObject&ServerRequestInterface
     */
    private function mockRequest()
    {
        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::at(0))
            ->method('getAttribute')
            ->with(self::equalTo('versionString'))
            ->willReturn('v3');

        $request->expects(self::at(1))
            ->method('getAttribute')
            ->with(self::equalTo('pageSlug'))
            ->willReturn('getting-started');

        $request->expects(self::at(2))
            ->method('getUri')
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
            '/software/ansel-craft/documentation'
        );

        return $uri;
    }
}
