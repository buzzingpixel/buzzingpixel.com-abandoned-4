<?php

declare(strict_types=1);

namespace Tests\App\HttpRouteMiddleware\RequireAdmin;

use App\Content\Meta\MetaPayload;
use App\HttpRouteMiddleware\RequireAdmin\RequireAdminAction;
use App\HttpRouteMiddleware\RequireAdmin\RequireAdminResponder;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Throwable;
use function func_get_args;

class RequireAdminActionTest extends TestCase
{
    /** @var RequireAdminAction */
    private $action;

    /** @var MockObject&ServerRequestInterface */
    private $request;
    /** @var Response */
    private $response;
    /** @var MockObject&RequestHandlerInterface */
    private $handler;

    /** @var UserModel|null */
    private $user;

    /** @var mixed[] */
    private $responderCallArgs = [];

    /**
     * @throws Throwable
     */
    public function testWhenNoUser() : void
    {
        $this->user = null;

        $this->internalSetUp();

        $response = ($this->action)(
            $this->request,
            $this->handler
        );

        self::assertSame($this->response, $response);

        self::assertCount(1, $this->responderCallArgs);

        self::assertInstanceOf(
            MetaPayload::class,
            $this->responderCallArgs[0]
        );

        self::assertSame(
            'Unauthorized',
            $this->responderCallArgs[0]->getMetaTitle()
        );
    }

    /**
     * @throws Throwable
     */
    public function testWhenUserIsNotAdmin() : void
    {
        $this->user = new UserModel();

        $this->internalSetUp();

        $response = ($this->action)(
            $this->request,
            $this->handler
        );

        self::assertSame($this->response, $response);

        self::assertCount(1, $this->responderCallArgs);

        self::assertInstanceOf(
            MetaPayload::class,
            $this->responderCallArgs[0]
        );

        self::assertSame(
            'Unauthorized',
            $this->responderCallArgs[0]->getMetaTitle()
        );
    }

    /**
     * @throws Throwable
     */
    public function testWhenUserIsAdmin() : void
    {
        $this->user = new UserModel(['isAdmin' => true]);

        $this->internalSetUp();

        $response = ($this->action)(
            $this->request,
            $this->handler
        );

        self::assertSame($this->response, $response);

        self::assertCount(0, $this->responderCallArgs);
    }

    private function internalSetUp() : void
    {
        $this->response = new Response();

        $this->request = $this->createMock(
            ServerRequestInterface::class
        );

        $this->handler = $this->createMock(
            RequestHandlerInterface::class
        );

        if ($this->user === null || ! $this->user->isAdmin()) {
            $this->handler->expects(self::never())
                ->method(self::anything());
        } else {
            $this->handler->expects(self::once())
                ->method('handle')
                ->with(self::equalTo($this->request))
                ->willReturn($this->response);
        }

        $this->action = new RequireAdminAction(
            $this->mockResponder($this->response),
            $this->mockUserApi()
        );
    }

    /**
     * @return RequireAdminResponder&MockObject
     */
    private function mockResponder(ResponseInterface $response) : RequireAdminResponder
    {
        $this->responderCallArgs = [];

        $mock = $this->createMock(
            RequireAdminResponder::class
        );

        if ($this->user === null || ! $this->user->isAdmin()) {
            $mock->expects(self::once())
                ->method('__invoke')
                ->willReturnCallback(
                    function () use ($response) : ResponseInterface {
                        $this->responderCallArgs = func_get_args();

                        return $response;
                    }
                );
        } else {
            $mock->expects(self::never())
                ->method(self::anything());
        }

        return $mock;
    }

    /**
     * @return UserApi&MockObject
     */
    private function mockUserApi() : UserApi
    {
        $mock = $this->createMock(UserApi::class);

        $mock->method('fetchLoggedInUser')->willReturn(
            $this->user
        );

        return $mock;
    }
}
