<?php

declare(strict_types=1);

namespace App\Schedule\Transformers;

use App\Persistence\Constants;
use App\Persistence\Schedule\ScheduleTrackingRecord;
use App\Schedule\Frequency;
use App\Schedule\Models\ScheduleItemModel;
use DateTimeImmutable;
use Throwable;
use function constant;
use function in_array;

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
        }

        if ($record->last_run_end_at !== '') {
            $lastRunEndAt = DateTimeImmutable::createFromFormat(
                Constants::POSTGRES_OUTPUT_FORMAT,
                $record->last_run_end_at
            );
        }

        try {
            $runEvery = (string) constant($record->class . '::RUN_EVERY');
        } catch (Throwable $e) {
            $runEvery = Frequency::ALWAYS;
        }

        return new ScheduleItemModel([
            'id' => $record->id,
            'class' => $record->class,
            'runEvery' => $runEvery,
            'isRunning' => in_array(
                $record->is_running,
                [
                    '1',
                    1,
                    'true',
                    true,
                ]
            ),
            'lastRunStartAt' => $lastRunStartAt,
            'lastRunEndAt' => $lastRunEndAt,
        ]);
    }
}
