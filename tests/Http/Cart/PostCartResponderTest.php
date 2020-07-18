<?php

declare(strict_types=1);

namespace Tests\Http\Cart;

use App\Http\Cart\PostCartResponder;
use App\Payload\Payload;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Flash\Messages;
use Tests\TestConfig;

class PostCartResponderTest extends TestCase
{
    public function testWhenNotSuccess(): void
    {
        $flashMessages = $this->createMock(Messages::class);

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('PostMessage'),
                self::equalTo([
                    'status' => Payload::STATUS_NOT_VALID,
                    'result' => ['message' => 'We could not process your order'],
                ])
            );

        $responseFactory = TestConfig::$di->get(
            ResponseFactoryInterface::class
        );

        $responder = new PostCartResponder(
            $flashMessages,
            $responseFactory
        );

        $payload = new Payload(Payload::STATUS_NOT_VALID);

        $response = $responder($payload);

        self::assertSame(
            303,
            $response->getStatusCode(),
        );

        $locationHeader = $response->getHeader('Location');

        self::assertCount(1, $locationHeader);

        self::assertSame(
            '/cart',
            $locationHeader[0]
        );
    }

    public function test(): void
    {
        $flashMessages = $this->createMock(Messages::class);

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('PostMessage'),
                self::equalTo(['status' => Payload::STATUS_SUCCESSFUL])
            );

        $responseFactory = TestConfig::$di->get(
            ResponseFactoryInterface::class
        );

        $responder = new PostCartResponder(
            $flashMessages,
            $responseFactory
        );

        $payload = new Payload(
            Payload::STATUS_SUCCESSFUL,
            ['orderId' => 'foo-id']
        );

        $response = $responder($payload);

        self::assertSame(
            303,
            $response->getStatusCode(),
        );

        $locationHeader = $response->getHeader('Location');

        self::assertCount(1, $locationHeader);

        self::assertSame(
            '/account/purchases/view/foo-id',
            $locationHeader[0]
        );
    }
}
