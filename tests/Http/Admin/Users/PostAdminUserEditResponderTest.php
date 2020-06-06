<?php

declare(strict_types=1);

namespace Tests\Http\Admin\Users;

use App\Http\Admin\Users\PostAdminUserEditResponder;
use App\Payload\Payload;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Flash\Messages;
use Tests\TestConfig;

class PostAdminUserEditResponderTest extends TestCase
{
    public function testWhenNotUpdated() : void
    {
        $flashMessages = $this->createMock(Messages::class);

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('PostMessage'),
                self::equalTo([
                    'status' => Payload::STATUS_NOT_VALID,
                    'result' => ['test'],
                ])
            );

        $responseFactory = TestConfig::$di->get(ResponseFactoryInterface::class);

        $responder = new PostAdminUserEditResponder(
            $flashMessages,
            $responseFactory
        );

        $payload = new Payload(
            Payload::STATUS_NOT_VALID,
            ['test']
        );

        $response = $responder($payload, 'foo-id');

        self::assertSame(
            303,
            $response->getStatusCode(),
        );

        $locationHeader = $response->getHeader('Location');

        self::assertCount(1, $locationHeader);

        self::assertSame(
            '/admin/users/edit/foo-id',
            $locationHeader[0]
        );
    }

    public function testWhenUpdated() : void
    {
        $flashMessages = $this->createMock(Messages::class);

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('PostMessage'),
                self::equalTo([
                    'status' => Payload::STATUS_SUCCESSFUL,
                    'result' => ['message' => 'Successfully created new user'],
                ])
            );

        $responseFactory = TestConfig::$di->get(ResponseFactoryInterface::class);

        $responder = new PostAdminUserEditResponder(
            $flashMessages,
            $responseFactory
        );

        $payload = new Payload(
            Payload::STATUS_UPDATED,
            ['test']
        );

        $response = $responder($payload, 'bar-id');

        self::assertSame(
            303,
            $response->getStatusCode(),
        );

        $locationHeader = $response->getHeader('Location');

        self::assertCount(1, $locationHeader);

        self::assertSame(
            '/admin/users/view/bar-id',
            $locationHeader[0]
        );
    }
}
