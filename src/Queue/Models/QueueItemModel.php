<?php

declare(strict_types=1);

namespace App\Queue\Models;

use DateTimeImmutable;

class QueueItemModel
{
    public string $id = '';

    public QueueModel $queue;

    public bool $isFinished = false;

    public ?DateTimeImmutable $finishedAt;

    public string $class = '';

    public string $method = '__invoke';

    /** @var mixed[]|null */
    public ?array $context;
}
