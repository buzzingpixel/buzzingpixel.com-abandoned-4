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

        if ($model->getLastRunStartAt() !== null) {
            $record->last_run_start_at = $model->getLastRunStartAt()
                ->format(DateTimeInterface::ATOM);
        }

        if ($model->getLastRunEndAt() !== null) {
            $record->last_run_end_at = $model->getLastRunEndAt()
                ->format(DateTimeInterface::ATOM);
        }

        return $record;
    }
}
