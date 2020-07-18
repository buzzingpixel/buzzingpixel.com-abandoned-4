<?php

declare(strict_types=1);

namespace Tests\Http\Account\PaymentMethods\Delete;

use App\Http\Account\PaymentMethods\Delete\PostDeletePaymentMethodResponder;
use App\Payload\Payload;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Flash\Messages as FlashMessages;
use Tests\TestConfig;

class PostDeletePaymentMethodResponderTest extends TestCase
{
    public function testWhenNotDeleted() : void
    {
        $payload = new Payload(
            Payload::STATUS_ERROR,
            ['foo' => 'bar']
        );

        $flashMessages = $this->createMock(
            FlashMessages::class,
        );

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('PostMessage'),
                self::equalTo([
                    'status' => $payload->getStatus(),
                    'result' => $payload->getResult(),
                ]),
            );

        $responder = new PostDeletePaymentMethodResponder(
            $flashMessages,
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            ),
        );

        $response = $responder($payload);

        self::assertSame(303, $response->getStatusCode());

        self::assertCount(1, $response->getHeaders());

        $location = $response->getHeader('Location');

        self::assertCount(1, $location);

        self::assertSame(
            '/account/payment-methods',
            $location[0],
        );
    }

    public function testWhenDeleted() : void
    {
        $payload = new Payload(
            Payload::STATUS_DELETED,
            ['foo' => 'bar']
        );

        $flashMessages = $this->createMock(
            FlashMessages::class,
        );

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('PostMessage'),
                self::equalTo([
                    'status' => Payload::STATUS_SUCCESSFUL,
                    'result' => ['message' => 'Payment method deleted'],
                ]),
            );

        $responder = new PostDeletePaymentMethodResponder(
            $flashMessages,
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            ),
        );

        $response = $responder($payload);

        self::assertSame(303, $response->getStatusCode());

        self::assertCount(1, $response->getHeaders());

        $location = $response->getHeader('Location');

        self::assertCount(1, $location);

        self::assertSame(
            '/account/payment-methods',
            $location[0],
        );
    }
}
