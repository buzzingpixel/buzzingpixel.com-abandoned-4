<?php

declare(strict_types=1);

namespace Tests\Http\Software;

use App\Content\Meta\MetaPayload;
use App\Content\Software\SoftwareInfoPayload;
use App\Http\Software\GetChangelogItemResponder;
use MJErwin\ParseAChangelog\Release;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ResponseFactory;
use Tests\TestConfig;
use Throwable;
use Twig\Environment as TwigEnvironment;

class GetChangelogItemResponderTest extends TestCase
{
    private GetChangelogItemResponder $responder;

    private MetaPayload $metaPayload;
    /** @var Release&MockObject */
    private $release;
    private SoftwareInfoPayload $softwareInfoPayload;
    private string $uriPath;

    /**
     * @throws Throwable
     */
    public function test(): void
    {
        $response = ($this->responder)(
            $this->metaPayload,
            $this->release,
            $this->softwareInfoPayload,
            $this->uriPath
        );

        self::assertSame(200, $response->getStatusCode());

        self::assertSame(
            ['EnableStaticCache' => ['true']],
            $response->getHeaders()
        );

        self::assertSame(
            'TwigRenderOutput',
            $response->getBody()->__toString()
        );
    }

    protected function setUp(): void
    {
        $this->metaPayload = new MetaPayload();

        $this->release = $this->createMock(Release::class);

        $this->softwareInfoPayload = new SoftwareInfoPayload();

        $this->uriPath = '/foo/bar/uri/path';

        $this->responder = new GetChangelogItemResponder(
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
                self::equalTo(
                    'Http/Software/ChangelogItem.twig'
                ),
                self::equalTo([
                    'metaPayload' => $this->metaPayload,
                    'release' => $this->release,
                    'softwareInfoPayload' => $this->softwareInfoPayload,
                    'uriPath' => $this->uriPath,
                    'activeNavHref' => $this->uriPath . '/changelog',
                ])
            )
            ->willReturn('TwigRenderOutput');

        return $twigEnvironment;
    }
}
