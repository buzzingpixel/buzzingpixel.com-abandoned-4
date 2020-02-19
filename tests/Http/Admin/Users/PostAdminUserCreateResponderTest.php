<?php

declare(strict_types=1);

namespace Tests\Http\Admin\Users;

use App\Http\Admin\Users\PostAdminUserCreateResponder;
use App\Payload\Payload;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Flash\Messages as FlashMessages;
use Tests\TestConfig;

class PostAdminUserCreateResponderTest extends TestCase
{
    public function testWhenNotCreated() : void
    {
        $payload = new Payload(
            Payload::STATUS_ERROR,
            ['foo' => 'bar']
        );

        $flashMessages = $this->createMock(
            FlashMessages::class
        );

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('PostMessage'),
                self::equalTo(
                    [
                        'status' => Payload::STATUS_ERROR,
                        'result' => ['foo' => 'bar'],
                    ]
                ),
            );

        $responseFactory = TestConfig::$di->get(
            ResponseFactoryInterface::class
        );

        $responder = new PostAdminUserCreateResponder(
            $flashMessages,
            $responseFactory,
        );

        $response = $responder($payload);

        self::assertSame(303, $response->getStatusCode());

        $header = $response->getHeader('Location');

        self::assertCount(1, $header);

        self::assertSame(
            '/admin/users/create',
            $header[0],
        );
    }

    public function test() : void
    {
        $payload = new Payload(Payload::STATUS_CREATED);

        $flashMessages = $this->createMock(
            FlashMessages::class
        );

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('PostMessage'),
                self::equalTo(
                    [
                        'status' => Payload::STATUS_SUCCESSFUL,
                        'result' => ['message' => 'Successfully created new user'],
                    ]
                ),
            );

        $responseFactory = TestConfig::$di->get(
            ResponseFactoryInterface::class
        );

        $responder = new PostAdminUserCreateResponder(
            $flashMessages,
            $responseFactory,
        );

        $response = $responder($payload);

        self::assertSame(303, $response->getStatusCode());

        $header = $response->getHeader('Location');

        self::assertCount(1, $header);

        self::assertSame(
            '/admin/users',
            $header[0],
        );
    }
}
