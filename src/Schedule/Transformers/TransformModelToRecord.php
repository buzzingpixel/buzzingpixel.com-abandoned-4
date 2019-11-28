<?php

declare(strict_types=1);

namespace App\Schedule\Transformers;

use App\Persistence\Schedule\ScheduleTrackingRecord;
use App\Schedule\Models\ScheduleItemModel;
use DateTimeInterface;

class TransformModelToRecord
{
    public function __invoke(ScheduleItemModel $model) : ScheduleTrackingRecord
    {
        $record = new ScheduleTrackingRecord();

        $record->id = $model->getId();

        $record->class = $model->getClass();

        $record->is_running = $model->isRunning() ? '1' : '0';

        $record->last_run_start_at = '';

        $record->last_run_end_at = '';

        $lastRunStartAt = $model->getLastRunStartAt();

        if ($lastRunStartAt !== null) {
            $record->last_run_start_at = $lastRunStartAt
                ->format(DateTimeInterface::ATOM);
        }

        $lastRunEndAt = $model->getLastRunEndAt();

        if ($lastRunEndAt !== null) {
            $record->last_run_end_at = $lastRunEndAt
                ->format(DateTimeInterface::ATOM);
        }

        return $record;
    }
}
