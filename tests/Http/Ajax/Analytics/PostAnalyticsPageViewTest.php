<?php

declare(strict_types=1);

namespace Tests\Http\Ajax\Analytics;

use App\Analytics\AnalyticsApi;
use App\Analytics\Models\AnalyticsModel;
use App\Http\Ajax\Analytics\PostAnalyticsPageViewAction;
use App\Payload\Payload;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use buzzingpixel\cookieapi\Cookie;
use buzzingpixel\cookieapi\interfaces\CookieApiInterface;
use buzzingpixel\cookieapi\interfaces\CookieInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tests\TestConfig;
use Throwable;

class PostAnalyticsPageViewTest extends TestCase
{
    public function testWhenUserIsAdmin(): void
    {
        $user = new UserModel();

        $user->isAdmin = true;

        $cookie = new Cookie('activity_id', 'foo-cookie-id');

        $cookieApi = $this->createMock(
            CookieApiInterface::class,
        );

        $cookieApi->expects(self::never())
            ->method(self::anything());

        $cookieApi->expects(self::never())
            ->method('saveCookie');

        $uuidFactory = $this->createMock(
            UuidFactoryWithOrderedTimeCodec::class,
        );

        $uuidFactory->expects(self::never())
            ->method(self::anything());

        $userApi = $this->createMock(UserApi::class);

        $userApi->method('fetchLoggedInUser')
            ->willReturn($user);

        $analyticsApi = $this->createMock(
            AnalyticsApi::class,
        );

        $analyticsApi->expects(self::never())
            ->method(self::anything());

        $request = $this->createMock(
            ServerRequestInterface::class,
        );

        $request->method('getParsedBody')
            ->willReturn([
                'uri' => 'foo-uri',
                'foo' => 'bar',
            ]);

        $action = new PostAnalyticsPageViewAction(
            $cookieApi,
            $uuidFactory,
            $userApi,
            $analyticsApi,
            TestConfig::$di->get(
                ResponseFactoryInterface::class,
            )
        );

        $response = $action($request);

        self::assertSame(
            200,
            $response->getStatusCode(),
        );

        $headers = $response->getHeaders();

        self::assertCount(1, $headers);

        $header = $headers['Content-type'];

        self::assertCount(1, $header);

        self::assertSame(
            'application/json',
            $header[0],
        );

        self::assertSame(
            '{"status":"ok"}',
            $response->getBody()->__toString()
        );
    }

    public function testWhenCookieExists(): void
    {
        $user = new UserModel();

        $cookie = new Cookie('activity_id', 'foo-cookie-id');

        $cookieApi = $this->createMock(
            CookieApiInterface::class,
        );

        $cookieApi->method('retrieveCookie')
            ->with(self::equalTo('activity_id'))
            ->willReturn($cookie);

        $cookieApi->expects(self::never())
            ->method('makeCookie');

        $cookieApi->expects(self::never())
            ->method('saveCookie');

        $uuidFactory = $this->createMock(
            UuidFactoryWithOrderedTimeCodec::class,
        );

        $uuidFactory->expects(self::never())
            ->method(self::anything());

        $userApi = $this->createMock(UserApi::class);

        $userApi->method('fetchLoggedInUser')
            ->willReturn($user);

        $analyticsApi = $this->createMock(
            AnalyticsApi::class,
        );

        $analyticsApi->expects(self::once())
            ->method('createPageView')
            ->willReturnCallback(
                static function (AnalyticsModel $model) use (
                    $cookie,
                    $user
                ): Payload {
                    self::assertSame(
                        $cookie,
                        $model->cookie,
                    );

                    self::assertSame(
                        $user,
                        $model->user,
                    );

                    self::assertTrue(
                        $model->wasLoggedInOnPageLoad
                    );

                    self::assertSame(
                        'foo-uri',
                        $model->uri,
                    );

                    return new Payload(Payload::STATUS_CREATED);
                }
            );

        $request = $this->createMock(
            ServerRequestInterface::class,
        );

        $request->method('getParsedBody')
            ->willReturn([
                'uri' => 'foo-uri',
                'foo' => 'bar',
            ]);

        $action = new PostAnalyticsPageViewAction(
            $cookieApi,
            $uuidFactory,
            $userApi,
            $analyticsApi,
            TestConfig::$di->get(
                ResponseFactoryInterface::class,
            )
        );

        $response = $action($request);

        self::assertSame(
            200,
            $response->getStatusCode(),
        );

        $headers = $response->getHeaders();

        self::assertCount(1, $headers);

        $header = $headers['Content-type'];

        self::assertCount(1, $header);

        self::assertSame(
            'application/json',
            $header[0],
        );

        self::assertSame(
            '{"status":"ok"}',
            $response->getBody()->__toString()
        );
    }

    /**
     * @throws Throwable
     */
    public function testWhenNoCookie(): void
    {
        $uuid = TestConfig::$di->get(
            UuidFactoryWithOrderedTimeCodec::class,
        )->uuid1();

        $cookie = new Cookie('activity_id', $uuid->toString());

        $cookieApi = $this->createMock(
            CookieApiInterface::class,
        );

        $cookieApi->method('retrieveCookie')
            ->with(self::equalTo('activity_id'))
            ->willReturn(null);

        $cookieApi->method('makeCookie')
            ->with(
                self::equalTo('activity_id'),
                self::equalTo($uuid->toString()),
            )
            ->willReturn($cookie);

        $cookieApi->expects(self::once())
            ->method('saveCookie')
            ->willReturnCallback(
                static function (CookieInterface $incomingCookie) use (
                    $cookie
                ): void {
                    self::assertSame(
                        $cookie,
                        $incomingCookie
                    );
                }
            );

        $uuidFactory = $this->createMock(
            UuidFactoryWithOrderedTimeCodec::class,
        );

        $uuidFactory->method('uuid1')
            ->willReturn($uuid);

        $userApi = $this->createMock(UserApi::class);

        $userApi->method('fetchLoggedInUser')
            ->willReturn(null);

        $analyticsApi = $this->createMock(
            AnalyticsApi::class,
        );

        $analyticsApi->expects(self::once())
            ->method('createPageView')
            ->willReturnCallback(
                static function (AnalyticsModel $model) use (
                    $cookie
                ): Payload {
                    self::assertSame(
                        $cookie,
                        $model->cookie,
                    );

                    self::assertNull($model->user);

                    self::assertFalse(
                        $model->wasLoggedInOnPageLoad
                    );

                    self::assertSame(
                        '/',
                        $model->uri,
                    );

                    return new Payload(Payload::STATUS_CREATED);
                }
            );

        $request = $this->createMock(
            ServerRequestInterface::class,
        );

        $request->method('getParsedBody')
            ->willReturn('');

        $action = new PostAnalyticsPageViewAction(
            $cookieApi,
            $uuidFactory,
            $userApi,
            $analyticsApi,
            TestConfig::$di->get(
                ResponseFactoryInterface::class,
            )
        );

        $response = $action($request);

        self::assertSame(
            200,
            $response->getStatusCode(),
        );

        $headers = $response->getHeaders();

        self::assertCount(1, $headers);

        $header = $headers['Content-type'];

        self::assertCount(1, $header);

        self::assertSame(
            'application/json',
            $header[0],
        );

        self::assertSame(
            '{"status":"ok"}',
            $response->getBody()->__toString()
        );
    }
}
