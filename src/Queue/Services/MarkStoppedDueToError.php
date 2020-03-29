<?php

declare(strict_types=1);

namespace App\Queue\Services;

use App\Persistence\SaveExistingRecord;
use App\Queue\Models\QueueModel;
use App\Queue\Transformers\TransformQueueModelToRecord;
use DateTimeImmutable;
use DateTimeZone;
use Throwable;
use const PHP_EOL;

class MarkStoppedDueToError
{
    private TransformQueueModelToRecord $queueModeToRecord;
    private SaveExistingRecord $saveExistingRecord;

    public function __construct(
        TransformQueueModelToRecord $queueModeToRecord,
        SaveExistingRecord $saveExistingRecord
    ) {
        $this->queueModeToRecord  = $queueModeToRecord;
        $this->saveExistingRecord = $saveExistingRecord;
    }

    public function __invoke(
        QueueModel $model,
        ?Throwable $e = null
    ) : void {
        $msg = '';

        if ($e !== null) {
            $eol  = PHP_EOL . PHP_EOL;
            $msg  = 'Error Code: ' . $e->getCode() . $eol;
            $msg .= 'File: ' . $e->getFile() . $eol;
            $msg .= 'Line: ' . $e->getLine() . $eol;
            $msg .= 'Message: ' . $e->getMessage() . $eol;
            $msg .= 'Trace . ' . $e->getTraceAsString();
        }

        $model->isRunning          = false;
        $model->isFinished         = true;
        $model->finishedDueToError = true;
        $model->errorMessage       = $msg;
        $model->finishedAt         = new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC'),
        );

        $record = ($this->queueModeToRecord)($model);

        ($this->saveExistingRecord)($record);
    }
}
