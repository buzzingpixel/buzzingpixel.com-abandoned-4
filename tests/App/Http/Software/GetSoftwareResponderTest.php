<?php

declare(strict_types=1);

namespace Tests\App\Http\Software;

use App\Content\Meta\MetaPayload;
use App\Content\Modules\ModulePayload;
use App\Content\Software\SoftwareInfoPayload;
use App\Http\Software\GetSoftwareResponder;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ResponseFactory;
use Throwable;
use Twig\Environment as TwigEnvironment;

class GetSoftwareResponderTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $metaPayload = new MetaPayload();

        $modulePayload = new ModulePayload();

        $softwareInfoPayload = new SoftwareInfoPayload();

        $twigEnvironment = $this->createMock(TwigEnvironment::class);

        $twigEnvironment->expects(self::once())
            ->method('render')
            ->with(
                self::equalTo('SoftwarePage.twig'),
                self::equalTo([
                    'metaPayload' => $metaPayload,
                    'modulePayload' => $modulePayload,
                    'softwareInfoPayload' => $softwareInfoPayload,
                    'uriPath' => '/foo/var',
                ])
            )
            ->willReturn('TwigRenderReturnContent');

        $responseFactory = new ResponseFactory();

        $response = (new GetSoftwareResponder(
            $responseFactory,
            $twigEnvironment,
        ))(
            $metaPayload,
            $modulePayload,
            $softwareInfoPayload,
            '/foo/var'
        );

        self::assertSame(200, $response->getStatusCode());

        self::assertSame(
            'TwigRenderReturnContent',
            (string) $response->getBody()
        );
    }
}
