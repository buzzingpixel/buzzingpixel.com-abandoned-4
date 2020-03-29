<?php

declare(strict_types=1);

namespace App\Queue\Transformers;

use App\Persistence\Constants;
use App\Persistence\Queue\QueueItemRecord;
use App\Queue\Models\QueueItemModel;
use App\Queue\Models\QueueModel;
use DateTimeImmutable;
use Throwable;
use function assert;
use function in_array;
use function json_decode;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class QueueItemRecordToModel
{
    public function __invoke(
        QueueItemRecord $record,
        QueueModel $queueModel
    ) : QueueItemModel {
        $model = new QueueItemModel();

        $model->id = $record->id;

        $queueModel->addItem($model);

        $model->runOrder = (int) $record->run_order;

        $model->isFinished = in_array(
            $record->is_finished,
            ['1', 1, 'true', true],
            true,
        );

        try {
            $finishedAt = DateTimeImmutable::createFromFormat(
                Constants::POSTGRES_OUTPUT_FORMAT,
                $record->finished_at,
            );

            assert($finishedAt instanceof DateTimeImmutable);

            $model->finishedAt = $finishedAt;
        } catch (Throwable $e) {
            $model->finishedAt = null;
        }

        $model->class = $record->class;

        $model->method = $record->method;

        $model->context = json_decode($record->context, true);

        return $model;
    }
}
