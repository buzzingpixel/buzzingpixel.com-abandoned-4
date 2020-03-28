<?php

declare(strict_types=1);

namespace Tests\Queue;

use App\Payload\Payload;
use App\Queue\Models\QueueModel;
use App\Queue\QueueApi;
use App\Queue\Services\AddToQueue;
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
}
