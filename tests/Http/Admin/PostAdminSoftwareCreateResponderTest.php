<?php

declare(strict_types=1);

namespace Tests\Http\Admin;

use App\Http\Admin\PostAdminSoftwareCreateResponder;
use App\Payload\Payload;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Flash\Messages as FlashMessages;
use stdClass;
use function func_get_args;

class PostAdminSoftwareCreateResponderTest extends TestCase
{
    public function testWhenNotCreated() : void
    {
        $flashMessagesCallHolder       = new stdClass();
        $flashMessagesCallHolder->args = [];

        $flashMessages = $this->createMock(
            FlashMessages::class
        );

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->willReturnCallback(static function () use (
                $flashMessagesCallHolder
            ) : void {
                $flashMessagesCallHolder->args = func_get_args();
            });

        $response2 = $this->createMock(
            ResponseInterface::class
        );

        $response = $this->createMock(
            ResponseInterface::class
        );

        $response->expects(self::once())
            ->method('withHeader')
            ->with(
                self::equalTo('Location'),
                self::equalTo('/admin/software/create')
            )
            ->willReturn($response2);

        $responseFactory = $this->createMock(
            ResponseFactoryInterface::class
        );

        $responseFactory->expects(self::once())
            ->method('createResponse')
            ->with(self::equalTo(303))
            ->willReturn($response);

        $responder = new PostAdminSoftwareCreateResponder(
            $flashMessages,
            $responseFactory
        );

        $payload = new Payload(Payload::STATUS_ERROR);

        $returnResponse = $responder($payload);

        self::assertSame($response2, $returnResponse);

        self::assertCount(
            2,
            $flashMessagesCallHolder->args
        );

        self::assertSame(
            'PostMessage',
            $flashMessagesCallHolder->args[0]
        );

        self::assertSame(
            [
                'status' => Payload::STATUS_ERROR,
                'result' => [],
            ],
            $flashMessagesCallHolder->args[1]
        );
    }

    public function testWhenCreated() : void
    {
        $flashMessagesCallHolder       = new stdClass();
        $flashMessagesCallHolder->args = [];

        $flashMessages = $this->createMock(
            FlashMessages::class
        );

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->willReturnCallback(static function () use (
                $flashMessagesCallHolder
            ) : void {
                $flashMessagesCallHolder->args = func_get_args();
            });

        $response2 = $this->createMock(
            ResponseInterface::class
        );

        $response = $this->createMock(
            ResponseInterface::class
        );

        $response->expects(self::once())
            ->method('withHeader')
            ->with(
                self::equalTo('Location'),
                self::equalTo('/admin/software')
            )
            ->willReturn($response2);

        $responseFactory = $this->createMock(
            ResponseFactoryInterface::class
        );

        $responseFactory->expects(self::once())
            ->method('createResponse')
            ->with(self::equalTo(303))
            ->willReturn($response);

        $responder = new PostAdminSoftwareCreateResponder(
            $flashMessages,
            $responseFactory
        );

        $payload = new Payload(Payload::STATUS_CREATED);

        $returnResponse = $responder($payload);

        self::assertSame($response2, $returnResponse);

        self::assertCount(
            2,
            $flashMessagesCallHolder->args
        );

        self::assertSame(
            'PostMessage',
            $flashMessagesCallHolder->args[0]
        );

        self::assertSame(
            [
                'status' => Payload::STATUS_SUCCESSFUL,
                'result' => ['message' => 'Successfully created new software'],
            ],
            $flashMessagesCallHolder->args[1]
        );
    }
}
