<?php

declare(strict_types=1);

namespace App\Queue\Services;

use App\Payload\Payload;
use App\Persistence\SaveExistingRecord;
use App\Queue\Models\QueueModel;
use App\Queue\Transformers\TransformQueueModelToRecord;
use Exception;

class MarkItemAsStarted
{
    private TransformQueueModelToRecord $queueModelToRecord;
    private SaveExistingRecord $saveExistingRecord;

    public function __construct(
        TransformQueueModelToRecord $queueModelToRecord,
        SaveExistingRecord $saveExistingRecord
    ) {
        $this->queueModelToRecord = $queueModelToRecord;
        $this->saveExistingRecord = $saveExistingRecord;
    }

    public function __invoke(QueueModel $model) : void
    {
        $model->hasStarted = true;

        $model->isRunning = true;

        $record = ($this->queueModelToRecord)($model);

        $payload = ($this->saveExistingRecord)($record);

        if ($payload->getStatus() === Payload::STATUS_UPDATED) {
            return;
        }

        throw new Exception('An unknown error occured');
    }
}
