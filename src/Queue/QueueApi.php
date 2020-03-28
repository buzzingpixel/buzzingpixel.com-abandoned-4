<?php

declare(strict_types=1);

namespace App\Queue;

use App\Queue\Models\QueueModel;
use App\Queue\Services\AddToQueue;
use Psr\Container\ContainerInterface;
use function assert;

class QueueApi
{
    private ContainerInterface $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function addToQueue(QueueModel $queueModel) : void
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(AddToQueue::class);

        assert($service instanceof AddToQueue);

        $service($queueModel);
    }
}
