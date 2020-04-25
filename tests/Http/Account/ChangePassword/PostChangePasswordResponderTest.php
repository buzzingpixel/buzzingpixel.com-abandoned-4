<?php

declare(strict_types=1);

namespace Tests\Http\Account\ChangePassword;

use App\Http\Account\ChangePassword\PostChangePasswordResponder;
use App\Payload\Payload;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Flash\Messages as FlashMessages;
use Tests\TestConfig;

class PostChangePasswordResponderTest extends TestCase
{
    public function testWhenNotUpdated() : void
    {
        $payload = new Payload(Payload::STATUS_ERROR, [
            'test' => 'result',
            'more' => 'testing',
        ]);

        $flashMessages = $this->createMock(
            FlashMessages::class
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

        $responder = new PostChangePasswordResponder(
            $flashMessages,
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            )
        );

        $response = $responder($payload);

        self::assertSame(303, $response->getStatusCode());

        $headers = $response->getHeaders();

        self::assertCount(1, $headers);

        self::assertCount(1, $headers['Location']);

        self::assertSame(
            '/account/change-password',
            $headers['Location'][0],
        );
    }

    public function test() : void
    {
        $payload = new Payload(Payload::STATUS_UPDATED, [
            'test' => 'result',
            'more' => 'testing',
        ]);

        $flashMessages = $this->createMock(
            FlashMessages::class
        );

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('LoginFormMessage'),
                self::equalTo([
                    'status' => Payload::STATUS_SUCCESSFUL,
                    'result' => [
                        'message' => 'Your password was updated successfully.' .
                            ' You can now log in with your new password.',
                    ],
                ]),
            );

        $responder = new PostChangePasswordResponder(
            $flashMessages,
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            )
        );

        $response = $responder($payload);

        self::assertSame(303, $response->getStatusCode());

        $headers = $response->getHeaders();

        self::assertCount(1, $headers);

        self::assertCount(1, $headers['Location']);

        self::assertSame(
            '/account',
            $headers['Location'][0],
        );
    }
}
