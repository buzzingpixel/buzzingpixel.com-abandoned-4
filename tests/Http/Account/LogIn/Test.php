<?php

declare(strict_types=1);

namespace Tests\Http\Account\LogIn;

use App\Http\Account\LogIn\GetLogOutAction;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tests\TestConfig;

class Test extends TestCase
{
    public function testWithNoRedirect() : void
    {
        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('logCurrentUserOut');

        $action = new GetLogOutAction(
            $userApi,
            TestConfig::$di->get(
                ResponseFactoryInterface::class,
            ),
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn(null);

        $response = $action($request);

        self::assertSame(303, $response->getStatusCode());

        $headers = $response->getHeaders();

        self::assertCount(1, $headers);

        self::assertCount(1, $headers['Location']);

        self::assertSame($headers['Location'][0], '/');
    }

    public function testWithRedirect() : void
    {
        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('logCurrentUserOut');

        $action = new GetLogOutAction(
            $userApi,
            TestConfig::$di->get(
                ResponseFactoryInterface::class,
            ),
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn(['redirect_to' => '/foo/url']);

        $response = $action($request);

        self::assertSame(303, $response->getStatusCode());

        $headers = $response->getHeaders();

        self::assertCount(1, $headers);

        self::assertCount(1, $headers['Location']);

        self::assertSame($headers['Location'][0], '/foo/url');
    }
}
