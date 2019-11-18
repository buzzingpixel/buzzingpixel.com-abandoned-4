<?php

declare(strict_types=1);

namespace Tests\App\Http\Software;

use App\Content\Meta\MetaPayload;
use App\Content\Software\SoftwareInfoPayload;
use App\Http\Software\GetChangelogItemResponder;
use App\HttpResponse\Minifier;
use MJErwin\ParseAChangelog\Release;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ResponseFactory;
use Tests\TestConfig;
use Throwable;
use Twig\Environment as TwigEnvironment;

class GetChangelogItemResponderTest extends TestCase
{
    /** @var GetChangelogItemResponder */
    private $responder;

    /** @var MetaPayload */
    private $metaPayload;
    /** @var Release&MockObject */
    private $release;
    /** @var SoftwareInfoPayload */
    private $softwareInfoPayload;
    /** @var string */
    private $uriPath;

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $response = ($this->responder)(
            $this->metaPayload,
            $this->release,
            $this->softwareInfoPayload,
            $this->uriPath
        );

        self::assertSame(200, $response->getStatusCode());

        self::assertSame(
            'MinifierOutput',
            $response->getBody()->__toString()
        );
    }

    protected function setUp() : void
    {
        $this->metaPayload = new MetaPayload();

        $this->release = $this->createMock(Release::class);

        $this->softwareInfoPayload = new SoftwareInfoPayload();

        $this->uriPath = '/foo/bar/uri/path';

        $this->responder = new GetChangelogItemResponder(
            TestConfig::$di->get(ResponseFactory::class),
            $this->mockTwigEnvironment(),
            $this->mockMinifier()
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
                self::equalTo('SoftwareChangelogItemPage.twig'),
                self::equalTo([
                    'metaPayload' => $this->metaPayload,
                    'release' => $this->release,
                    'softwareInfoPayload' => $this->softwareInfoPayload,
                    'uriPath' => $this->uriPath,
                    'activeHref' => $this->uriPath . '/changelog',
                ])
            )
            ->willReturn('TwigRenderOutput');

        return $twigEnvironment;
    }

    /**
     * @return Minifier&MockObject
     */
    private function mockMinifier()
    {
        $minifier = $this->createMock(Minifier::class);

        $minifier->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo('TwigRenderOutput'))
            ->willReturn('MinifierOutput');

        return $minifier;
    }
}
