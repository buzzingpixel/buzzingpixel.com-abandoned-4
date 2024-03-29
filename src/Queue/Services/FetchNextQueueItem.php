<?php

declare(strict_types=1);

namespace App\Queue\Services;

use App\Persistence\Queue\QueueRecord;
use App\Persistence\RecordQueryFactory;
use App\Queue\Models\QueueItemModel;

use function assert;

class FetchNextQueueItem
{
    private FetchHelper $fetchHelper;
    private RecordQueryFactory $recordQueryFactory;

    public function __construct(
        FetchHelper $fetchHelper,
        RecordQueryFactory $recordQueryFactory
    ) {
        $this->fetchHelper        = $fetchHelper;
        $this->recordQueryFactory = $recordQueryFactory;
    }

    public function __invoke(): ?QueueItemModel
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

        $model = $this->fetchHelper->processRecords([$record])[0];

        foreach ($model->items as $item) {
            if ($item->isFinished) {
                continue;
            }

            return $item;
        }

        return null;
    }
}
