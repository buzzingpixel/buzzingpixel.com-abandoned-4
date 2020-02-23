<?php

declare(strict_types=1);

namespace Tests\Http\Cart;

use App\Cart\CartApi;
use App\Http\Cart\GetAddToCartAction;
use App\Software\Models\SoftwareModel;
use App\Software\SoftwareApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Tests\TestConfig;
use Throwable;
use function assert;

class GetAddToCartActionTest extends TestCase
{
    public function testWhenNoSoftware() : void
    {
        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareBySlug')
            ->with(self::equalTo('foo-slug'))
            ->willReturn(null);

        $cartApi = $this->createMock(CartApi::class);

        $cartApi->expects(self::never())
            ->method(self::anything());

        $action = new GetAddToCartAction(
            $softwareApi,
            $cartApi,
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            ),
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('slug'))
            ->willReturn('foo-slug');

        $exception = null;

        try {
            $action($request);
        } catch (HttpNotFoundException $e) {
            $exception = $e;
        }

        assert($exception instanceof HttpNotFoundException);

        self::assertSame($request, $exception->getRequest());
    }

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $software = new SoftwareModel();

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareBySlug')
            ->with(self::equalTo('foo-slug'))
            ->willReturn($software);

        $cartApi = $this->createMock(CartApi::class);

        $cartApi->expects(self::once())
            ->method('addItemToCurrentUsersCart')
            ->with(self::equalTo($software));

        $action = new GetAddToCartAction(
            $softwareApi,
            $cartApi,
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            ),
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('slug'))
            ->willReturn('foo-slug');

        $response = $action($request);

        self::assertSame(303, $response->getStatusCode());

        $headers = $response->getHeaders();

        self::assertCount(1, $headers);

        self::assertCount(1, $headers['Location']);

        self::assertSame($headers['Location'][0], '/cart');
    }
}
