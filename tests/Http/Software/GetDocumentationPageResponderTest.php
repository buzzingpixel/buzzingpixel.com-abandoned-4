<?php

declare(strict_types=1);

namespace Tests\Http\Software;

use App\Content\Documentation\DocumentationPagePayload;
use App\Content\Documentation\DocumentationVersionPayload;
use App\Content\Documentation\DocumentationVersionsPayload;
use App\Content\Meta\MetaPayload;
use App\Http\Software\GetDocumentationPageResponder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ResponseFactory;
use Tests\TestConfig;
use Throwable;
use Twig\Environment as TwigEnvironment;

class GetDocumentationPageResponderTest extends TestCase
{
    private GetDocumentationPageResponder $responder;

    private MetaPayload $metaPayload;
    /** @var DocumentationPagePayload&MockObject */
    private $activePage;
    /** @var DocumentationVersionPayload&MockObject */
    private $activeVersion;
    /** @var DocumentationVersionsPayload&MockObject */
    private $versions;

    private string $activePageSlug = '';

    /**
     * @throws Throwable
     */
    public function testWhenNoActivePageSlug() : void
    {
        $this->activePageSlug = '';

        $this->internalSetUp();

        $response = ($this->responder)(
            'software/ansel-craft/documentation',
            $this->metaPayload,
            $this->activePage,
            $this->activeVersion,
            $this->versions
        );

        self::assertSame(200, $response->getStatusCode());

        self::assertSame(
            'TwigRenderOutput',
            $response->getBody()->__toString()
        );
    }

    /**
     * @throws Throwable
     */
    public function testWithActivePageSlug() : void
    {
        $this->activePageSlug = 'test-page-slug';

        $this->internalSetUp();

        $response = ($this->responder)(
            'software/ansel-craft/documentation',
            $this->metaPayload,
            $this->activePage,
            $this->activeVersion,
            $this->versions
        );

        self::assertSame(200, $response->getStatusCode());

        self::assertSame(
            'TwigRenderOutput',
            $response->getBody()->__toString()
        );
    }

    protected function internalSetUp() : void
    {
        $this->metaPayload = new MetaPayload();

        $this->activePage = $this->mockActivePage();

        $this->activeVersion = $this->mockActiveVersion();

        $this->versions = $this->mockVersions();

        $this->responder = new GetDocumentationPageResponder(
            TestConfig::$di->get(ResponseFactory::class),
            $this->mockTwigEnvironment(),
        );
    }

    /**
     * @return MockObject&TwigEnvironment
     */
    private function mockTwigEnvironment()
    {
        $twigEnvironment = $this->createMock(
            TwigEnvironment::class
        );

        $twigEnvironment->expects(self::once())
            ->method('render')
            ->with(
                self::equalTo('DocumentationPage.twig'),
                self::equalTo([
                    'uriPath' => 'software/ansel-craft/documentation',
                    'activeHref' => 'software/ansel-craft/documentation/documentation',
                    'metaPayload' => $this->metaPayload,
                    'activePage' => $this->activePage,
                    'activeVersion' => $this->activeVersion,
                    'versions' => $this->versions,
                ])
            )
            ->willReturn('TwigRenderOutput');

        return $twigEnvironment;
    }

    /**
     * @return DocumentationPagePayload&MockObject
     */
    private function mockActivePage()
    {
        $activePage = $this->createMock(
            DocumentationPagePayload::class
        );

        $activePage->method('getSlug')->willReturn(
            $this->activePageSlug
        );

        return $activePage;
    }

    /**
     * @return DocumentationVersionPayload&MockObject
     */
    private function mockActiveVersion()
    {
        $activeVersion = $this->createMock(
            DocumentationVersionPayload::class
        );

        $activeVersion->method('getSlug')
            ->willReturn('test-version-slug');

        return $activeVersion;
    }

    /**
     * @return DocumentationVersionsPayload&MockObject
     */
    private function mockVersions()
    {
        return $this->createMock(
            DocumentationVersionsPayload::class
        );
    }
}
