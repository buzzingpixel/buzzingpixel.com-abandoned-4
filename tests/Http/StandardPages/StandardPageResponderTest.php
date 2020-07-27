<?php

declare(strict_types=1);

namespace Tests\Http\StandardPages;

use App\Content\Meta\MetaPayload;
use App\Content\Modules\ModulePayload;
use App\Http\StandardPages\StandardPageResponder;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ResponseFactory;
use Throwable;
use Twig\Environment as TwigEnvironment;

class StandardPageResponderTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test(): void
    {
        $metaPayload = new MetaPayload();

        $modulePayload = new ModulePayload();

        $twigEnvironment = $this->createMock(
            TwigEnvironment::class
        );

        $twigEnvironment->expects(self::once())
            ->method('render')
            ->with(
                self::equalTo('Http/StandardPage.twig'),
                self::equalTo([
                    'metaPayload' => $metaPayload,
                    'modulePayload' => $modulePayload,
                ])
            )
            ->willReturn('TwigRenderReturnContent');

        $responseFactory = new ResponseFactory();

        $response = (new StandardPageResponder(
            $responseFactory,
            $twigEnvironment,
        ))($metaPayload, $modulePayload);

        self::assertSame(200, $response->getStatusCode());

        self::assertSame(
            ['EnableStaticCache' => ['true']],
            $response->getHeaders()
        );

        self::assertSame(
            'TwigRenderReturnContent',
            (string) $response->getBody()
        );
    }
}
