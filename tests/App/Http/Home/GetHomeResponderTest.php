<?php

declare(strict_types=1);

namespace Tests\App\Http\Home;

use App\Content\Meta\MetaPayload;
use App\Content\Modules\ModulePayload;
use App\Http\Home\GetHomeResponder;
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

        $response = (new GetHomeResponder(
            $responseFactory,
            $twigEnvironment,
        ))($metaPayload, $modulePayload);

        self::assertSame(200, $response->getStatusCode());

        self::assertSame(
            'TwigRenderReturnContent',
            (string) $response->getBody()
        );
    }
}
