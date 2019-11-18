<?php

declare(strict_types=1);

namespace Tests\App\Http\Home;

use App\Content\Meta\MetaPayload;
use App\Content\Modules\ModulePayload;
use App\Http\Home\GetHomeResponder;
use App\HttpResponse\Minifier;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ResponseFactory;
use Throwable;
use Twig\Environment as TwigEnvironment;

class GetHomeResponderTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $metaPayload = new MetaPayload();

        $modulePayload = new ModulePayload();

        $twigEnvironment = $this->createMock(TwigEnvironment::class);

        $twigEnvironment->expects(self::once())
            ->method('render')
            ->with(
                self::equalTo('StandardPage.twig'),
                self::equalTo([
                    'metaPayload' => $metaPayload,
                    'modulePayload' => $modulePayload,
                ])
            )
            ->willReturn('TwigRenderReturnContent');

        $responseFactory = new ResponseFactory();

        $minifier = $this->createMock(Minifier::class);

        $minifier->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo('TwigRenderReturnContent'))
            ->willReturn('MinifierReturnContent');

        $response = (new GetHomeResponder(
            $responseFactory,
            $twigEnvironment,
            $minifier
        ))($metaPayload, $modulePayload);

        self::assertSame(200, $response->getStatusCode());

        self::assertSame('MinifierReturnContent', (string) $response->getBody());
    }
}
