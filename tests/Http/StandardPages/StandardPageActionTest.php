<?php

declare(strict_types=1);

namespace Tests\Http\StandardPages;

use App\Content\Meta\ExtractMetaFromPath;
use App\Content\Meta\MetaPayload;
use App\Content\Modules\ExtractModulesFromPath;
use App\Content\Modules\ModulePayload;
use App\Http\StandardPages\StandardPageAction;
use App\Http\StandardPages\StandardPageResponder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;

use function assert;

class StandardPageActionTest extends TestCase
{
    public function testWhenNoPath(): void
    {
        $responder = $this->createMock(
            StandardPageResponder::class,
        );

        $extractMetaFromPath = $this->createMock(
            ExtractMetaFromPath::class
        );

        $extractMetaFromPath->expects(self::never())
            ->method(self::anything());

        $extractModulesFromPath = $this->createMock(
            ExtractModulesFromPath::class,
        );

        $extractModulesFromPath->expects(self::never())
            ->method(self::anything());

        $request = $this->createMock(
            ServerRequestInterface::class,
        );

        $uri = $this->createMock(UriInterface::class);

        $uri->method('getPath')->willReturn('foo-bar');

        $request->method('getUri')->willReturn($uri);

        $action = new StandardPageAction(
            $responder,
            $extractMetaFromPath,
            $extractModulesFromPath,
        );

        $exception = null;

        try {
            $action($request);
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof HttpNotFoundException);

        self::assertSame($request, $exception->getRequest());
    }

    /**
     * @throws Throwable
     */
    public function test(): void
    {
        $metaPayload = new MetaPayload();

        $extractMetaFromPath = $this->createMock(
            ExtractMetaFromPath::class
        );

        $extractMetaFromPath->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo('Terms'))
            ->willReturn($metaPayload);

        $modulePayload = new ModulePayload();

        $extractModulesFromPath = $this->createMock(
            ExtractModulesFromPath::class,
        );

        $extractModulesFromPath->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo('Terms'))
            ->willReturn($modulePayload);

        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            StandardPageResponder::class,
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo($metaPayload),
                self::equalTo($modulePayload)
            )
            ->willReturn($response);

        $request = $this->createMock(
            ServerRequestInterface::class,
        );

        $uri = $this->createMock(UriInterface::class);

        $uri->method('getPath')->willReturn('/terms');

        $request->method('getUri')->willReturn($uri);

        $action = new StandardPageAction(
            $responder,
            $extractMetaFromPath,
            $extractModulesFromPath,
        );

        self::assertSame(
            $response,
            $action($request)
        );
    }
}
