<?php

declare(strict_types=1);

namespace Tests\HttpRouteMiddleware\RequireLogIn;

use App\Content\Meta\MetaPayload;
use App\HttpRouteMiddleware\RequireLogIn\RequireLogInAction;
use App\HttpRouteMiddleware\RequireLogIn\RequireLoginResponder;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\RequestHandlerInterface;
use stdClass;
use Throwable;
use function func_get_args;

class RequireLogInActionTest extends TestCase
{
    private RequireLogInAction $action;

    /** @var UserApi&MockObject */
    private $userApi;
    /** @var RequireLoginResponder&MockObject */
    private $responder;
    /** @var MockObject&ServerRequestInterface */
    private $request;
    /** @var MockObject&RequestHandlerInterface */
    private $handler;
    /** @var MockObject&ResponseInterface */
    private $response;

    /**
     * @throws Throwable
     */
    public function testWhenNoUser() : void
    {
        $argHolder = new stdClass();

        $argHolder->args = [];

        $this->userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn(null);

        $this->responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                function () use ($argHolder) : ResponseInterface {
                    $argHolder->args = func_get_args();

                    return $this->response;
                }
            );

        $this->handler->expects(self::never())
            ->method(self::anything());

        $response = ($this->action)(
            $this->request,
            $this->handler
        );

        self::assertSame($this->response, $response);

        /** @var mixed[] $args */
        $args = $argHolder->args;

        self::assertCount(2, $args);

        /** @var MetaPayload|null $metaPayLoad */
        $metaPayLoad = $args[0];

        self::assertInstanceOf(MetaPayload::class, $metaPayLoad);

        self::assertSame('Log In', $metaPayLoad->getMetaTitle());

        self::assertSame('/foo/path', $args[1]);
    }

    /**
     * @throws Throwable
     */
    public function testWhenUser() : void
    {
        $this->userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn(new UserModel());

        $this->responder->expects(self::never())
            ->method(self::anything());

        $this->handler->expects(self::once())
            ->method('handle')
            ->with(self::equalTo($this->request))
            ->willReturn($this->response);

        $response = ($this->action)(
            $this->request,
            $this->handler
        );

        self::assertSame($this->response, $response);
    }

    protected function setUp() : void
    {
        $this->userApi = $this->createMock(UserApi::class);

        $this->responder = $this->createMock(
            RequireLoginResponder::class
        );

        $uri = $this->createMock(UriInterface::class);

        $uri->method('getPath')->willReturn('/foo/path');

        $this->request = $this->createMock(
            ServerRequestInterface::class
        );

        $this->request->method('getUri')->willReturn($uri);

        $this->handler = $this->createMock(
            RequestHandlerInterface::class
        );

        $this->response = $this->createMock(
            ResponseInterface::class
        );

        $this->action = new RequireLogInAction(
            $this->userApi,
            $this->responder
        );
    }
}
