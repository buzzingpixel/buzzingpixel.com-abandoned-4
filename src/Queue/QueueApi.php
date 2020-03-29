<?php

declare(strict_types=1);

namespace App\Queue;

use App\Payload\Payload;
use App\Queue\Models\QueueItemModel;
use App\Queue\Models\QueueModel;
use App\Queue\Services\AddToQueue;
use App\Queue\Services\FetchNextQueueItem;
use App\Queue\Services\MarkItemAsStarted;
use App\Queue\Services\MarkStoppedDueToError;
use App\Queue\Services\PostRun;
use App\Queue\Services\RunItem;
use Psr\Container\ContainerInterface;
use Throwable;
use function assert;

class QueueApi
{
    private ContainerInterface $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function addToQueue(QueueModel $queueModel) : Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(AddToQueue::class);

        assert($service instanceof AddToQueue);

        return $service($queueModel);
    }

    public function fetchNextQueueItem() : ?QueueItemModel
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchNextQueueItem::class);

        assert($service instanceof FetchNextQueueItem);

        return $service();
    }

    public function markAsStarted(QueueModel $queueModel) : void
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(MarkItemAsStarted::class);

        assert($service instanceof MarkItemAsStarted);

        $service($queueModel);
    }

    public function runItem(QueueItemModel $item) : void
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(RunItem::class);

        assert($service instanceof RunItem);

        $service($item);
    }

    public function postRun(QueueItemModel $item) : void
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(PostRun::class);

        assert($service instanceof PostRun);

        $service($item);
    }

    public function markStoppedDueToError(
        QueueModel $queueModel,
        ?Throwable $exception = null
    ) : void {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(MarkStoppedDueToError::class);

        assert($service instanceof MarkStoppedDueToError);

        $service($queueModel, $exception);
    }
}
