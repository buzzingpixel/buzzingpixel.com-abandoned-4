<?php

declare(strict_types=1);

namespace App\Queue\Services;

use App\Queue\Models\QueueModel;
use function dd;

class AddToQueue
{
    public function __invoke(QueueModel $queueModel) : void
    {
        dd($queueModel);
    }
}
