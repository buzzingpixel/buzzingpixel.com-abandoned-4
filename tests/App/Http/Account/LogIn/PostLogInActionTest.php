<?php

declare(strict_types=1);

namespace Tests\App\Http\Account\LogIn;

use App\Http\Account\LogIn\PostLogInAction;
use App\Http\Account\LogIn\PostLogInResponder;
use App\Payload\Payload;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use function func_get_args;

class PostLogInActionTest extends TestCase
{
    /** @var PostLogInAction */
    private $action;
    /** @var MockObject&ServerRequestInterface */
    private $request;
    /** @var Response */
    private $response;

    /** @var mixed[] */
    private $postData = [
        'email_address' => 'foo@bar.baz',
        'password' => 'foopass',
        'redirect_to' => '/foo/redirect',
    ];

    /** @var Payload */
    private $logInPayload;

    /** @var UserModel|null */
    private $user;

    /** @var mixed[] */
    private $responderCallArgs;

    public function testWhenUserIsNull() : void
    {
        $this->user = null;

        $this->internalSetUp();

        $response = ($this->action)($this->request);

        self::assertSame($this->response, $response);

        $args = $this->responderCallArgs;

        self::assertCount(2, $args);

        /** @var Payload|null $arg1 */
        $arg1 = $args[0];
        self::assertInstanceOf(Payload::class, $arg1);
        self::assertSame(
            Payload::STATUS_ERROR,
            $arg1->getStatus()
        );
        self::assertSame(
            ['message' => 'Unable to log you in with that email address and password'],
            $arg1->getResult()
        );

        self::assertSame(
            '/foo/redirect',
            $this->responderCallArgs[1]
        );
    }

    public function testWhenLogUserInHasError() : void
    {
        $this->user = new UserModel();

        $this->internalSetUp(Payload::STATUS_ERROR);

        $response = ($this->action)($this->request);

        self::assertSame($this->response, $response);

        $args = $this->responderCallArgs;

        self::assertCount(2, $args);

        /** @var Payload|null $arg1 */
        $arg1 = $args[0];
        self::assertInstanceOf(Payload::class, $arg1);
        self::assertSame(
            Payload::STATUS_ERROR,
            $arg1->getStatus()
        );
        self::assertSame(
            ['message' => 'Unable to log you in with that email address and password'],
            $arg1->getResult()
        );

        self::assertSame(
            '/foo/redirect',
            $this->responderCallArgs[1]
        );
    }

    public function test() : void
    {
        $this->user = new UserModel();

        $this->internalSetUp();

        $response = ($this->action)($this->request);

        self::assertSame($this->response, $response);

        $args = $this->responderCallArgs;

        self::assertCount(2, $args);

        self::assertSame(
            $this->logInPayload,
            $this->responderCallArgs[0]
        );

        self::assertSame(
            '/foo/redirect',
            $this->responderCallArgs[1]
        );
    }

    public function internalSetUp(
        string $loginPayloadStatus = Payload::STATUS_SUCCESSFUL
    ) : void {
        $this->logInPayload = new Payload($loginPayloadStatus);

        $this->request = $this->mockRequest();

        $this->response = new Response();

        $this->action = new PostLogInAction(
            $this->mockResponder($this->response),
            $this->mockUserApi()
        );
    }

    /**
     * @return MockObject&ServerRequestInterface
     */
    private function mockRequest() : ServerRequestInterface
    {
        $mock = $this->createMock(
            ServerRequestInterface::class
        );

        $mock->method('getParsedBody')->willReturn(
            $this->postData
        );

        return $mock;
    }

    /**
     * @return PostLogInResponder&MockObject
     */
    private function mockResponder(ResponseInterface $response) : PostLogInResponder
    {
        $this->responderCallArgs = [];

        $mock = $this->createMock(PostLogInResponder::class);

        $mock->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                function () use ($response) : ResponseInterface {
                    $this->responderCallArgs = func_get_args();

                    return $response;
                }
            );

        return $mock;
    }

    /**
     * @return UserApi&MockObject
     */
    private function mockUserApi() : UserApi
    {
        $mock = $this->createMock(UserApi::class);

        $mock->method('fetchUserByEmailAddress')
            ->with(self::equalTo(
                $this->postData['email_address'] ?? ''
            ))
            ->willReturn($this->user);

        $mock->method('logUserIn')
            ->with(
                self::equalTo($this->user),
                self::equalTo(
                    $this->postData['password'] ?? ''
                )
            )
            ->willReturn($this->logInPayload);

        return $mock;
    }
}
