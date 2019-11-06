<?php

declare(strict_types=1);

namespace Tests\App\Http\Software;

use App\Content\Meta\MetaPayload;
use App\Content\Modules\ModulePayload;
use App\Content\Software\SoftwareInfoPayload;
use App\Http\Software\GetSoftwareResponder;
use App\HttpRespose\Minifier;
use PHPUnit\Framework\MockObject\MockObject;
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

        /** @var TwigEnvironment&MockObject $twigEnvironment */
        $twigEnvironment = $this->createMock(TwigEnvironment::class);

        $twigEnvironment->expects(self::once())
            ->method('render')
            ->with(
                self::equalTo('SoftwarePage.twig'),
                self::equalTo([
                    'metaPayload' => $metaPayload,
                    'modulePayload' => $modulePayload,
                    'softwareInfoPayload' => $softwareInfoPayload,
                ])
            )
            ->willReturn('TwigRenderReturnContent');

        $responseFactory = new ResponseFactory();

        /** @var Minifier&MockObject $minifier */
        $minifier = $this->createMock(Minifier::class);

        $minifier->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo('TwigRenderReturnContent'))
            ->willReturn('MinifierReturnContent');

        $response = (new GetSoftwareResponder(
            $responseFactory,
            $twigEnvironment,
            $minifier
        ))(
            $metaPayload,
            $modulePayload,
            $softwareInfoPayload
        );

        self::assertSame(200, $response->getStatusCode());

        self::assertSame('MinifierReturnContent', (string) $response->getBody());
    }
}
