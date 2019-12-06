<?php

declare(strict_types=1);

namespace Tests\App\HttpRouteMiddleware\RequireLogIn;

use App\Content\Meta\MetaPayload;
use App\HttpResponse\Minifier;
use App\HttpRouteMiddleware\RequireLogIn\RequireLoginResponder;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ResponseFactory;
use Throwable;
use Twig\Environment as TwigEnvironment;

class RequireLoginResponderTest extends TestCase
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
                self::equalTo('LogIn.twig'),
                self::equalTo(
                    [
                        'metaPayload' => $metaPayload,
                        'redirectTo' => '/foo/bar/redirect',
                    ]
                )
            )
            ->willReturn('TwigRenderReturnContent');

        $responseFactory = new ResponseFactory();

        $minifier = $this->createMock(Minifier::class);

        $minifier->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo('TwigRenderReturnContent'))
            ->willReturn('MinifierReturnContent');

        $response = (new RequireLoginResponder(
            $responseFactory,
            $twigEnvironment,
            $minifier
        ))($metaPayload, '/foo/bar/redirect');

        self::assertSame(200, $response->getStatusCode());

        self::assertSame(
            'MinifierReturnContent',
            (string) $response->getBody()
        );
    }
}
