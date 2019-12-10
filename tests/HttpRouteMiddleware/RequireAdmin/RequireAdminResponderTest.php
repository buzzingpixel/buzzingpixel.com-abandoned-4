<?php

declare(strict_types=1);

namespace Tests\HttpRouteMiddleware\RequireAdmin;

use App\Content\Meta\MetaPayload;
use App\HttpRouteMiddleware\RequireAdmin\RequireAdminResponder;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ResponseFactory;
use Throwable;
use Twig\Environment as TwigEnvironment;

class RequireAdminResponderTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $metaPayload = new MetaPayload();

        $twigEnvironment = $this->createMock(TwigEnvironment::class);

        $twigEnvironment->expects(self::once())
            ->method('render')
            ->with(
                self::equalTo('Unauthorized.twig'),
                self::equalTo(
                    ['metaPayload' => $metaPayload]
                )
            )
            ->willReturn('TwigRenderReturnContent');

        $responseFactory = new ResponseFactory();

        $response = (new RequireAdminResponder(
            $responseFactory,
            $twigEnvironment,
        ))($metaPayload);

        self::assertSame(200, $response->getStatusCode());

        self::assertSame(
            'TwigRenderReturnContent',
            (string) $response->getBody()
        );
    }
}
