<?php

declare(strict_types=1);

namespace App\Queue\Transformers;

use App\Persistence\Constants;
use App\Persistence\Queue\QueueRecord;
use App\Queue\Models\QueueModel;
use DateTimeImmutable;
use Throwable;
use function assert;
use function in_array;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class QueueRecordToModel
{
    public function __invoke(QueueRecord $record) : QueueModel
    {
        $model = new QueueModel();

        $model->id = $record->id;

        $model->handle = $record->handle;

        $model->displayName = $record->display_name;

        $model->hasStarted = in_array(
            $record->has_started,
            ['1', 1, 'true', true],
            true,
        );

        $model->isRunning = in_array(
            $record->has_started,
            ['1', 1, 'true', true],
            true,
        );

        $assumeDeadAfter = DateTimeImmutable::createFromFormat(
            Constants::POSTGRES_OUTPUT_FORMAT,
            $record->assume_dead_after,
        );

        assert($assumeDeadAfter instanceof DateTimeImmutable);

        $model->isFinished = in_array(
            $record->is_finished,
            ['1', 1, 'true', true],
            true,
        );

        $model->finishedDueToError = in_array(
            $record->finished_due_to_error,
            ['1', 1, 'true', true],
            true,
        );

        $model->errorMessage = $record->error_message;

        $model->percentComplete = (float) $record->percent_complete;

        $addedAt = DateTimeImmutable::createFromFormat(
            Constants::POSTGRES_OUTPUT_FORMAT,
            $record->added_at,
        );

        assert($addedAt instanceof DateTimeImmutable);

        $model->addedAt = $addedAt;

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

        return $model;
    }
}
