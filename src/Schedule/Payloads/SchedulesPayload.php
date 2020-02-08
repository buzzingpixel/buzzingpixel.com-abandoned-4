<?php

declare(strict_types=1);

namespace App\Schedule\Payloads;

use App\Payload\SpecificPayload;
use App\Schedule\Models\ScheduleItemModel;

class SchedulesPayload extends SpecificPayload
{
    /** @var ScheduleItemModel[] */
    private array $schedules = [];

    /**
     * @param ScheduleItemModel[] $schedules
     */
    protected function setSchedules(array $schedules) : void
    {
        $this->schedules = $schedules;
    }

    /**
     * @return ScheduleItemModel[]
     */
    public function getSchedules() : array
    {
        return $this->schedules;
    }
}
