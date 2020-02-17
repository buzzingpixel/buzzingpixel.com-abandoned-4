<?php

declare(strict_types=1);

namespace Tests\Http\Admin\Software;

use App\Http\Admin\Software\PostAdminSoftwareAddVersionResponder;
use App\Payload\Payload;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Flash\Messages;
use Tests\TestConfig;

class PostAdminSoftwareAddVersionResponderTest extends TestCase
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

        $responder = new PostAdminSoftwareAddVersionResponder(
            $flashMessages,
            $responseFactory
        );

        $payload = new Payload(
            Payload::STATUS_NOT_VALID,
            ['test']
        );

        $response = $responder($payload, 'foo-slug');

        self::assertSame(
            303,
            $response->getStatusCode(),
        );

        $locationHeader = $response->getHeader('Location');

        self::assertCount(1, $locationHeader);

        self::assertSame(
            '/admin/software/foo-slug/add-version',
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
                    'result' => ['message' => 'Successfully edited software'],
                ])
            );

        $responseFactory = TestConfig::$di->get(ResponseFactoryInterface::class);

        $responder = new PostAdminSoftwareAddVersionResponder(
            $flashMessages,
            $responseFactory
        );

        $payload = new Payload(
            Payload::STATUS_UPDATED,
            ['test']
        );

        $response = $responder($payload, 'bar-slug');

        self::assertSame(
            303,
            $response->getStatusCode(),
        );

        $locationHeader = $response->getHeader('Location');

        self::assertCount(1, $locationHeader);

        self::assertSame(
            '/admin/software/view/bar-slug',
            $locationHeader[0]
        );
    }
}
