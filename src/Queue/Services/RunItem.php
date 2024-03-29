<?php

declare(strict_types=1);

namespace App\Queue\Services;

use App\Queue\Models\QueueItemModel;
use Psr\Container\ContainerInterface;

class RunItem
{
    private ContainerInterface $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function __invoke(QueueItemModel $item): void
    {
        /** @psalm-suppress MixedAssignment */
        $class = $this->di->get($item->class);

        /** @psalm-suppress MixedMethodCall */
        $class->{$item->method}($item->context);
    }
}
