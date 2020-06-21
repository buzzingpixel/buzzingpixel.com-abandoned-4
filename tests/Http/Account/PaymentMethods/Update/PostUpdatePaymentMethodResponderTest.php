<?php

declare(strict_types=1);

namespace Tests\Http\Account\PaymentMethods\Update;

use App\Http\Account\PaymentMethods\Update\PostUpdatePaymentMethodResponder;
use App\Payload\Payload;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Flash\Messages as FlashMessages;
use Tests\TestConfig;

class PostUpdatePaymentMethodResponderTest extends TestCase
{
    public function testWhenNotUpdated(): void
    {
        $id = 'foo-bar-id';

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

        $responder = new PostUpdatePaymentMethodResponder(
            $flashMessages,
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            ),
        );

        $response = $responder($payload, $id);

        self::assertSame(303, $response->getStatusCode());

        self::assertCount(1, $response->getHeaders());

        $location = $response->getHeader('Location');

        self::assertCount(1, $location);

        self::assertSame(
            '/account/payment-methods/' . $id,
            $location[0],
        );
    }

    public function testWhenCreated(): void
    {
        $payload = new Payload(
            Payload::STATUS_UPDATED,
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
                    'result' => ['message' => 'Payment method added'],
                ]),
            );

        $responder = new PostUpdatePaymentMethodResponder(
            $flashMessages,
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            ),
        );

        $response = $responder($payload, 'foo');

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
