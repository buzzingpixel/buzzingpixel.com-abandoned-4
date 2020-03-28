<?php

declare(strict_types=1);

namespace App\Queue;

use App\Payload\Payload;
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

    public function addToQueue(QueueModel $queueModel) : Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(AddToQueue::class);

        assert($service instanceof AddToQueue);

        return $service($queueModel);
    }
}
