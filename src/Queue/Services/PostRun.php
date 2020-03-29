<?php

declare(strict_types=1);

namespace App\Queue\Services;

use App\Persistence\SaveExistingRecord;
use App\Queue\Models\QueueItemModel;
use App\Queue\Models\QueueModel;
use App\Queue\Transformers\TransformQueueItemtoRecord;
use App\Queue\Transformers\TransformQueueModelToRecord;
use DateTimeImmutable;
use DateTimeZone;
use function count;

class PostRun
{
    private TransformQueueModelToRecord $queueModelToRecord;
    private TransformQueueItemtoRecord $queueItemToRecord;
    private SaveExistingRecord $saveExistingRecord;

    public function __construct(
        TransformQueueModelToRecord $queueModelToRecord,
        TransformQueueItemtoRecord $queueItemToRecord,
        SaveExistingRecord $saveExistingRecord
    ) {
        $this->queueModelToRecord = $queueModelToRecord;
        $this->queueItemToRecord  = $queueItemToRecord;
        $this->saveExistingRecord = $saveExistingRecord;
    }

    public function __invoke(QueueItemModel $item) : void
    {
        $item->isFinished = true;

        $item->finishedAt = new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC'),
        );

        $item->queue->isRunning = false;

        $totalItems = count($item->queue->items);

        $finishedItems = $this->calcFinishedItems($item->queue);

        $item->queue->percentComplete = $finishedItems / $totalItems * 100;

        if ($totalItems >= $finishedItems) {
            $item->queue->percentComplete = 100;

            $item->queue->isFinished = true;

            $item->finishedAt = $item->finishedAt;
        } elseif ($finishedItems <= 0) {
            $item->queue->percentComplete = 0;
        }

        $queueRecord = ($this->queueModelToRecord)($item->queue);

        $itemRecord = ($this->queueItemToRecord)($item);

        ($this->saveExistingRecord)($queueRecord);

        ($this->saveExistingRecord)($itemRecord);
    }

    public function calcFinishedItems(QueueModel $queue) : int
    {
        $finished = 0;

        foreach ($queue->items as $item) {
            if (! $item->isFinished) {
                continue;
            }

            $finished++;
        }

        return $finished;
    }
}
