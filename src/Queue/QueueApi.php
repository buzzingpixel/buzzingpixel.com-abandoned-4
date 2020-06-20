<?php

declare(strict_types=1);

namespace App\Queue;

use App\Payload\Payload;
use App\Queue\Models\QueueItemModel;
use App\Queue\Models\QueueModel;
use App\Queue\Services\AddToQueue;
use App\Queue\Services\CleanDeadItems;
use App\Queue\Services\CleanOldItems;
use App\Queue\Services\ClearAllStalledItems;
use App\Queue\Services\DeleteQueuesByIds;
use App\Queue\Services\FetchIncomplete;
use App\Queue\Services\FetchNextQueueItem;
use App\Queue\Services\FetchStalledItems;
use App\Queue\Services\MarkItemAsStarted;
use App\Queue\Services\MarkStoppedDueToError;
use App\Queue\Services\PostRun;
use App\Queue\Services\RestartAllStalledItems;
use App\Queue\Services\RestartQueuesByIds;
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

    public function addToQueue(QueueModel $queueModel): Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(AddToQueue::class);

        assert($service instanceof AddToQueue);

        return $service($queueModel);
    }

    public function fetchNextQueueItem(): ?QueueItemModel
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchNextQueueItem::class);

        assert($service instanceof FetchNextQueueItem);

        return $service();
    }

    /**
     * @throws Throwable
     */
    public function markAsStarted(QueueModel $queueModel): void
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(MarkItemAsStarted::class);

        assert($service instanceof MarkItemAsStarted);

        $service($queueModel);
    }

    public function runItem(QueueItemModel $item): void
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(RunItem::class);

        assert($service instanceof RunItem);

        $service($item);
    }

    public function postRun(QueueItemModel $item): void
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(PostRun::class);

        assert($service instanceof PostRun);

        $service($item);
    }

    public function markStoppedDueToError(
        QueueModel $queueModel,
        ?Throwable $exception = null
    ): void {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(MarkStoppedDueToError::class);

        assert($service instanceof MarkStoppedDueToError);

        $service($queueModel, $exception);
    }

    /**
     * @return QueueModel[]
     */
    public function fetchStalledItems(): array
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchStalledItems::class);

        assert($service instanceof FetchStalledItems);

        return $service();
    }

    /**
     * @return QueueModel[]
     */
    public function fetchIncomplete(): array
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchIncomplete::class);

        assert($service instanceof FetchIncomplete);

        return $service();
    }

    /**
     * @param string[] $ids
     */
    public function restartQueuesByIds(array $ids): void
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(RestartQueuesByIds::class);

        assert($service instanceof RestartQueuesByIds);

        $service($ids);
    }

    public function restartAllStalledItems(): void
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(RestartAllStalledItems::class);

        assert($service instanceof RestartAllStalledItems);

        $service();
    }

    /**
     * @param string[] $ids
     */
    public function deleteQueuesByIds(array $ids): void
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(DeleteQueuesByIds::class);

        assert($service instanceof DeleteQueuesByIds);

        $service($ids);
    }

    public function clearAllStalledItems(): void
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(ClearAllStalledItems::class);

        assert($service instanceof ClearAllStalledItems);

        $service();
    }

    /**
     * Returns the number of dead items cleaned
     */
    public function cleanDeadItems(): int
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(CleanDeadItems::class);

        assert($service instanceof CleanDeadItems);

        return $service();
    }

    /**
     * Returns the number of dead items cleaned
     */
    public function cleanOldItems(): int
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(CleanOldItems::class);

        assert($service instanceof CleanOldItems);

        return $service();
    }
}
