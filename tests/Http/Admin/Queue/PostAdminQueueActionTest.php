<?php

declare(strict_types=1);

namespace Tests\Http\Admin\Queue;

use App\Http\Admin\Queue\PostAdminQueueAction;
use App\Payload\Payload;
use App\Queue\QueueApi;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Flash\Messages as FlashMessages;
use Tests\TestConfig;
use function array_keys;
use function assert;

class PostAdminQueueActionTest extends TestCase
{
    public function testWhenActionIsEmpty() : void
    {
        $flashMessages = $this->createMock(FlashMessages::class);

        assert(
            $flashMessages instanceof FlashMessages &&
            $flashMessages instanceof MockObject
        );

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('PostMessage'),
                self::equalTo([
                    'status' => Payload::STATUS_ERROR,
                    'result' => ['message' => 'An action must be chosen'],
                ]),
            );

        $queueApi = $this->createMock(QueueApi::class);

        $queueApi->expects(self::never())->method(self::anything());

        assert(
            $queueApi instanceof QueueApi &&
            $queueApi instanceof MockObject
        );

        $action = new PostAdminQueueAction(
            $flashMessages,
            TestConfig::$di->get(ResponseFactoryInterface::class),
            $queueApi
        );

        $request = $this->createMock(ServerRequestInterface::class);

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([]);

        $returnResponse = $action($request);

        self::assertSame(303, $returnResponse->getStatusCode());

        self::assertSame(
            '/admin/queue',
            $returnResponse->getHeader('Location')[0],
        );
    }

    public function testNoItems() : void
    {
        $flashMessages = $this->createMock(FlashMessages::class);

        assert(
            $flashMessages instanceof FlashMessages &&
            $flashMessages instanceof MockObject
        );

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('PostMessage'),
                self::equalTo([
                    'status' => Payload::STATUS_ERROR,
                    'result' => ['message' => 'Items must be selected'],
                ]),
            );

        $queueApi = $this->createMock(QueueApi::class);

        $queueApi->expects(self::never())->method(self::anything());

        assert(
            $queueApi instanceof QueueApi &&
            $queueApi instanceof MockObject
        );

        $action = new PostAdminQueueAction(
            $flashMessages,
            TestConfig::$di->get(ResponseFactoryInterface::class),
            $queueApi
        );

        $request = $this->createMock(ServerRequestInterface::class);

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn(['action' => 'foo-bad-action']);

        $returnResponse = $action($request);

        self::assertSame(303, $returnResponse->getStatusCode());

        self::assertSame(
            '/admin/queue',
            $returnResponse->getHeader('Location')[0],
        );
    }

    public function testRestart() : void
    {
        $ids = [
            'id1' => 'true',
            'id2' => 'true',
        ];

        $flashMessages = $this->createMock(FlashMessages::class);

        assert(
            $flashMessages instanceof FlashMessages &&
            $flashMessages instanceof MockObject
        );

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('PostMessage'),
                self::equalTo([
                    'status' => Payload::STATUS_SUCCESSFUL,
                    'result' => ['message' => 'Items restarted'],
                ]),
            );

        $queueApi = $this->createMock(QueueApi::class);

        $queueApi->expects(self::once())
            ->method('restartQueuesByIds')
            ->with(self::equalTo(array_keys($ids)));

        assert(
            $queueApi instanceof QueueApi &&
            $queueApi instanceof MockObject
        );

        $action = new PostAdminQueueAction(
            $flashMessages,
            TestConfig::$di->get(ResponseFactoryInterface::class),
            $queueApi
        );

        $request = $this->createMock(ServerRequestInterface::class);

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'action' => 'restart',
                'selected_items' => $ids,
            ]);

        $returnResponse = $action($request);

        self::assertSame(303, $returnResponse->getStatusCode());

        self::assertSame(
            '/admin/queue',
            $returnResponse->getHeader('Location')[0],
        );
    }

    public function testRestartAll() : void
    {
        $flashMessages = $this->createMock(FlashMessages::class);

        assert(
            $flashMessages instanceof FlashMessages &&
            $flashMessages instanceof MockObject
        );

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('PostMessage'),
                self::equalTo([
                    'status' => Payload::STATUS_SUCCESSFUL,
                    'result' => ['message' => 'All stalled items restarted'],
                ]),
            );

        $queueApi = $this->createMock(QueueApi::class);

        $queueApi->expects(self::once())->method('restartAllStalledItems');

        assert(
            $queueApi instanceof QueueApi &&
            $queueApi instanceof MockObject
        );

        $action = new PostAdminQueueAction(
            $flashMessages,
            TestConfig::$di->get(ResponseFactoryInterface::class),
            $queueApi
        );

        $request = $this->createMock(ServerRequestInterface::class);

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn(['action' => 'restart_all']);

        $returnResponse = $action($request);

        self::assertSame(303, $returnResponse->getStatusCode());

        self::assertSame(
            '/admin/queue',
            $returnResponse->getHeader('Location')[0],
        );
    }

    public function testDelete() : void
    {
        $ids = [
            'id1' => 'true',
            'id2' => 'true',
        ];

        $flashMessages = $this->createMock(FlashMessages::class);

        assert(
            $flashMessages instanceof FlashMessages &&
            $flashMessages instanceof MockObject
        );

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('PostMessage'),
                self::equalTo([
                    'status' => Payload::STATUS_SUCCESSFUL,
                    'result' => ['message' => 'Items deleted'],
                ]),
            );

        $queueApi = $this->createMock(QueueApi::class);

        $queueApi->expects(self::once())
            ->method('deleteQueuesByIds')
            ->with(self::equalTo(array_keys($ids)));

        assert(
            $queueApi instanceof QueueApi &&
            $queueApi instanceof MockObject
        );

        $action = new PostAdminQueueAction(
            $flashMessages,
            TestConfig::$di->get(ResponseFactoryInterface::class),
            $queueApi
        );

        $request = $this->createMock(ServerRequestInterface::class);

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'action' => 'delete',
                'selected_items' => $ids,
            ]);

        $returnResponse = $action($request);

        self::assertSame(303, $returnResponse->getStatusCode());

        self::assertSame(
            '/admin/queue',
            $returnResponse->getHeader('Location')[0],
        );
    }

    public function testClearAll() : void
    {
        $flashMessages = $this->createMock(FlashMessages::class);

        assert(
            $flashMessages instanceof FlashMessages &&
            $flashMessages instanceof MockObject
        );

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('PostMessage'),
                self::equalTo([
                    'status' => Payload::STATUS_SUCCESSFUL,
                    'result' => ['message' => 'All stalled items cleared'],
                ]),
            );

        $queueApi = $this->createMock(QueueApi::class);

        $queueApi->expects(self::once())->method('clearAllStalledItems');

        assert(
            $queueApi instanceof QueueApi &&
            $queueApi instanceof MockObject
        );

        $action = new PostAdminQueueAction(
            $flashMessages,
            TestConfig::$di->get(ResponseFactoryInterface::class),
            $queueApi
        );

        $request = $this->createMock(ServerRequestInterface::class);

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn(['action' => 'clear_all']);

        $returnResponse = $action($request);

        self::assertSame(303, $returnResponse->getStatusCode());

        self::assertSame(
            '/admin/queue',
            $returnResponse->getHeader('Location')[0],
        );
    }

    public function testInvalidActionWithIds() : void
    {
        $ids = [
            'id1' => 'true',
            'id2' => 'true',
        ];

        $flashMessages = $this->createMock(FlashMessages::class);

        assert(
            $flashMessages instanceof FlashMessages &&
            $flashMessages instanceof MockObject
        );

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('PostMessage'),
                self::equalTo([
                    'status' => Payload::STATUS_ERROR,
                    'result' => ['message' => 'Unrecognized action'],
                ]),
            );

        $queueApi = $this->createMock(QueueApi::class);

        $queueApi->expects(self::never())->method(self::anything());

        assert(
            $queueApi instanceof QueueApi &&
            $queueApi instanceof MockObject
        );

        $action = new PostAdminQueueAction(
            $flashMessages,
            TestConfig::$di->get(ResponseFactoryInterface::class),
            $queueApi
        );

        $request = $this->createMock(ServerRequestInterface::class);

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'action' => 'foo-bad-action',
                'selected_items' => $ids,
            ]);

        $returnResponse = $action($request);

        self::assertSame(303, $returnResponse->getStatusCode());

        self::assertSame(
            '/admin/queue',
            $returnResponse->getHeader('Location')[0],
        );
    }
}
