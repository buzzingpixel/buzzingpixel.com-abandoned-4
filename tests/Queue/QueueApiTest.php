<?php

declare(strict_types=1);

namespace Tests\Queue;

use App\Payload\Payload;
use App\Queue\Models\QueueItemModel;
use App\Queue\Models\QueueModel;
use App\Queue\QueueApi;
use App\Queue\Services\AddToQueue;
use App\Queue\Services\FetchIncomplete;
use App\Queue\Services\FetchNextQueueItem;
use App\Queue\Services\FetchStalledItems;
use App\Queue\Services\MarkItemAsStarted;
use App\Queue\Services\MarkStoppedDueToError;
use App\Queue\Services\PostRun;
use App\Queue\Services\RunItem;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use function assert;

class QueueApiTest extends TestCase
{
    public function testAddToQueue() : void
    {
        $payload = new Payload(Payload::STATUS_SUCCESSFUL);

        $queueModel = new QueueModel();

        $service = $this->createMock(AddToQueue::class);

        $service->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($queueModel))
            ->willReturn($payload);

        assert(
            $service instanceof AddToQueue &&
            $service instanceof MockObject,
        );

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(self::equalTo(AddToQueue::class))
            ->willReturn($service);

        assert(
            $di instanceof ContainerInterface &&
            $di instanceof MockObject,
        );

        $api = new QueueApi($di);

        self::assertSame($payload, $api->addToQueue($queueModel));
    }

    public function testFetchNextQueueItem() : void
    {
        $queueItemModel = new QueueItemModel();

        $service = $this->createMock(FetchNextQueueItem::class);

        $service->expects(self::once())
            ->method('__invoke')
            ->willReturn($queueItemModel);

        assert(
            $service instanceof FetchNextQueueItem &&
            $service instanceof MockObject,
        );

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(self::equalTo(FetchNextQueueItem::class))
            ->willReturn($service);

        assert(
            $di instanceof ContainerInterface &&
            $di instanceof MockObject,
        );

        $api = new QueueApi($di);

        self::assertSame($queueItemModel, $api->fetchNextQueueItem());
    }

    public function testMarkAsStarted() : void
    {
        $queueModel = new QueueModel();

        $service = $this->createMock(MarkItemAsStarted::class);

        $service->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($queueModel));

        assert(
            $service instanceof MarkItemAsStarted &&
            $service instanceof MockObject,
        );

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(self::equalTo(MarkItemAsStarted::class))
            ->willReturn($service);

        assert(
            $di instanceof ContainerInterface &&
            $di instanceof MockObject,
        );

        $api = new QueueApi($di);

        $api->markAsStarted($queueModel);
    }

    public function testRunItem() : void
    {
        $queueItemModel = new QueueItemModel();

        $service = $this->createMock(RunItem::class);

        $service->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($queueItemModel));

        assert(
            $service instanceof RunItem &&
            $service instanceof MockObject,
        );

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(self::equalTo(RunItem::class))
            ->willReturn($service);

        assert(
            $di instanceof ContainerInterface &&
            $di instanceof MockObject,
        );

        $api = new QueueApi($di);

        $api->runItem($queueItemModel);
    }

    public function testPostRun() : void
    {
        $queueItemModel = new QueueItemModel();

        $service = $this->createMock(PostRun::class);

        $service->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($queueItemModel));

        assert(
            $service instanceof PostRun &&
            $service instanceof MockObject,
        );

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(self::equalTo(PostRun::class))
            ->willReturn($service);

        assert(
            $di instanceof ContainerInterface &&
            $di instanceof MockObject,
        );

        $api = new QueueApi($di);

        $api->postRun($queueItemModel);
    }

    public function testMarkStoppedDueToError() : void
    {
        $queueModel = new QueueModel();

        $exception = new Exception();

        $service = $this->createMock(MarkStoppedDueToError::class);

        $service->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($queueModel), self::equalTo($exception));

        assert(
            $service instanceof MarkStoppedDueToError &&
            $service instanceof MockObject,
        );

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(self::equalTo(MarkStoppedDueToError::class))
            ->willReturn($service);

        assert(
            $di instanceof ContainerInterface &&
            $di instanceof MockObject,
        );

        $api = new QueueApi($di);

        $api->markStoppedDueToError($queueModel, $exception);
    }

    public function testFetchStalledItems() : void
    {
        $queueModel1 = new QueueModel();
        $queueModel2 = new QueueModel();

        $service = $this->createMock(FetchStalledItems::class);

        $service->expects(self::once())
            ->method('__invoke')
            ->willReturn([$queueModel1, $queueModel2]);

        assert(
            $service instanceof FetchStalledItems &&
            $service instanceof MockObject,
        );

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(self::equalTo(FetchStalledItems::class))
            ->willReturn($service);

        assert(
            $di instanceof ContainerInterface &&
            $di instanceof MockObject,
        );

        $api = new QueueApi($di);

        self::assertSame(
            [$queueModel1, $queueModel2],
            $api->fetchStalledItems()
        );
    }

    public function testFetchIncomplete() : void
    {
        $queueModel1 = new QueueModel();
        $queueModel2 = new QueueModel();

        $service = $this->createMock(FetchIncomplete::class);

        $service->expects(self::once())
            ->method('__invoke')
            ->willReturn([$queueModel1, $queueModel2]);

        assert(
            $service instanceof FetchIncomplete &&
            $service instanceof MockObject,
        );

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(self::equalTo(FetchIncomplete::class))
            ->willReturn($service);

        assert(
            $di instanceof ContainerInterface &&
            $di instanceof MockObject,
        );

        $api = new QueueApi($di);

        self::assertSame(
            [$queueModel1, $queueModel2],
            $api->fetchIncomplete()
        );
    }
}
