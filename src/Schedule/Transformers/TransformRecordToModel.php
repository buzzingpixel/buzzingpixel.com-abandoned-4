<?php

declare(strict_types=1);

namespace App\Schedule\Transformers;

use App\Persistence\Constants;
use App\Persistence\Schedule\ScheduleTrackingRecord;
use App\Schedule\Frequency;
use App\Schedule\Models\ScheduleItemModel;
use DateTimeImmutable;
use Throwable;
use function assert;
use function constant;
use function in_array;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformRecordToModel
{
    public function __invoke(ScheduleTrackingRecord $record) : ScheduleItemModel
    {
        $lastRunStartAt = null;

        $lastRunEndAt = null;

        if ($record->last_run_start_at !== '') {
            $lastRunStartAt = DateTimeImmutable::createFromFormat(
                Constants::POSTGRES_OUTPUT_FORMAT,
                $record->last_run_start_at
            );

            assert($lastRunStartAt instanceof DateTimeImmutable);
        }

        if ($record->last_run_end_at !== '') {
            $lastRunEndAt = DateTimeImmutable::createFromFormat(
                Constants::POSTGRES_OUTPUT_FORMAT,
                $record->last_run_end_at
            );

            assert($lastRunEndAt instanceof DateTimeImmutable);
        }

        try {
            $runEvery = (string) constant($record->class . '::RUN_EVERY');
        } catch (Throwable $e) {
            $runEvery = Frequency::ALWAYS;
        }

        $model = new ScheduleItemModel();

        $model->id = $record->id;

        $model->class = $record->class;

        $model->checkRunEveryValue($runEvery);

        $model->runEvery = $runEvery;

        $model->isRunning = in_array(
            $record->is_running,
            [
                '1',
                1,
                'true',
                true,
            ],
            true
        );

        $model->lastRunStartAt = $lastRunStartAt;

        $model->lastRunEndAt = $lastRunEndAt;

        return $model;
    }
}
