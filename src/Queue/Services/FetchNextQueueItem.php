<?php

declare(strict_types=1);

namespace App\Queue\Services;

use App\Persistence\Queue\QueueItemRecord;
use App\Persistence\Queue\QueueRecord;
use App\Persistence\RecordQueryFactory;
use App\Queue\Models\QueueItemModel;
use App\Queue\Transformers\QueueItemRecordToModel;
use App\Queue\Transformers\QueueRecordToModel;
use function array_walk;
use function assert;

class FetchNextQueueItem
{
    private RecordQueryFactory $recordQueryFactory;
    private QueueRecordToModel $queuRecordToModel;
    private QueueItemRecordToModel $queueItemRecordToModel;

    public function __construct(
        RecordQueryFactory $recordQueryFactory,
        QueueRecordToModel $queuRecordToModel,
        QueueItemRecordToModel $queueItemRecordToModel
    ) {
        $this->recordQueryFactory     = $recordQueryFactory;
        $this->queuRecordToModel      = $queuRecordToModel;
        $this->queueItemRecordToModel = $queueItemRecordToModel;
    }

    public function __invoke() : ?QueueItemModel
    {
        $record = ($this->recordQueryFactory)(
            new QueueRecord()
        )
            ->withWhere('is_running', '0')
            ->withWhere('is_finished', '0')
            ->withOrder('added_at', 'asc')
            ->one();

        if ($record === null) {
            return null;
        }

        assert($record instanceof QueueRecord);

        $model = ($this->queuRecordToModel)($record);

        $itemRecords = ($this->recordQueryFactory)(
            new QueueItemRecord()
        )
            ->withWhere('queue_id', $model->id)
            ->withOrder('run_order', 'asc')
            ->all();

        array_walk(
            $itemRecords,
            fn(QueueItemRecord $record) => ($this->queueItemRecordToModel)(
                $record,
                $model
            ),
        );

        foreach ($model->items as $item) {
            if ($item->isFinished) {
                continue;
            }

            return $item;
        }

        return null;
    }
}
