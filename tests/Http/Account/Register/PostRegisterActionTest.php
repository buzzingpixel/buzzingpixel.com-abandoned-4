<?php

declare(strict_types=1);

namespace Tests\Http\Account\Register;

use App\Http\Account\Register\PostRegisterAction;
use App\Http\Account\Register\PostRegisterResponder;
use App\Payload\Payload;
use App\Users\Services\SaveUser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function func_get_args;

class PostRegisterActionTest extends TestCase
{
    private PostRegisterAction $action;

    /** @var MockObject&ResponseInterface */
    private $response;
    /** @var PostRegisterResponder&MockObject */
    private $responder;
    /** @var SaveUser&MockObject */
    private $saveUser;
    /** @var MockObject&ServerRequestInterface */
    private $request;

    /** @var mixed[] */
    private array $postData = [];

    /** @var mixed[] */
    private array $responderCallArgs = [];
    /** @var mixed[] */
    private array $saveUserCallArgs;

    public function testWhenPasswordsDoNotMatch() : void
    {
        $this->postData = [
            'password' => 'foopass',
            'confirm_password' => 'FooPassTwo',
        ];

        $this->internalSetUp();

        $response = ($this->action)($this->request);

        self::assertSame($this->response, $response);

        self::assertCount(2, $this->responderCallArgs);

        /** @var Payload|null $callPayload */
        $callPayload = $this->responderCallArgs[0];

        self::assertInstanceOf(Payload::class, $callPayload);

        self::assertSame(
            Payload::STATUS_NOT_VALID,
            $callPayload->getStatus()
        );

        self::assertSame(
            [
                'message' => 'Password confirmation must match password',
                'active' => 'register',
                'inputs' => [
                    'password' => 'Password must match Password Confirmation',
                    'confirmPassword' => 'Password Confirmation must match password',
                ],
            ],
            $callPayload->getResult()
        );

        self::assertSame('/', $this->responderCallArgs[1]);
    }

    public function test() : void
    {
        $this->postData = [
            'email_address' => 'foo@bar.baz',
            'password' => 'FooPass2019',
            'confirm_password' => 'FooPass2019',
            'redirect_to' => '/foo/redirect',
        ];

        $this->internalSetUp();

        $response = ($this->action)($this->request);

        self::assertSame($this->response, $response);

        self::assertCount(2, $this->responderCallArgs);

        /** @var Payload|null $callPayload */
        $callPayload = $this->responderCallArgs[0];

        self::assertInstanceOf(Payload::class, $callPayload);

        self::assertSame(
            Payload::STATUS_SUCCESSFUL,
            $callPayload->getStatus()
        );

        self::assertSame(
            [
                'message' => 'TestMessage',
                'active' => 'register',
                'inputs' =>
                    ['message' => 'TestMessage'],
            ],
            $callPayload->getResult()
        );

        self::assertSame('/foo/redirect', $this->responderCallArgs[1]);
    }

    public function internalSetUp() : void
    {
        $this->response = $this->createMock(
            ResponseInterface::class
        );

        $this->responderCallArgs = [];

        $this->responder = $this->createMock(
            PostRegisterResponder::class
        );

        $this->responder->method('__invoke')
            ->willReturnCallback(
                function () : ResponseInterface {
                    $this->responderCallArgs = func_get_args();

                    return $this->response;
                }
            );

        $this->saveUserCallArgs = [];

        $this->saveUser = $this->createMock(SaveUser::class);

        $this->saveUser->method('__invoke')
            ->willReturnCallback(
                function () {
                    $this->saveUserCallArgs = func_get_args();

                    return new Payload(
                        Payload::STATUS_SUCCESSFUL,
                        ['message' => 'TestMessage']
                    );
                }
            );

        $this->request = $this->createMock(
            ServerRequestInterface::class
        );

        $this->request->method('getParsedBody')
            ->willReturn($this->postData);

        $this->action = new PostRegisterAction(
            $this->responder,
            $this->saveUser
        );
    }
}
