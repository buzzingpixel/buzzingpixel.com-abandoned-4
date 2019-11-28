<?php

declare(strict_types=1);

namespace App\Persistence\Schedule;

use App\Persistence\Record;

class ScheduleTrackingRecord extends Record
{
    /** @var string */
    protected static $tableName = 'schedule_tracking';

    /** @var string */
    public $class = '';

    /** @var string int|bool|string */
    public $is_running = '0';

    /** @var string */
    public $last_run_start_at = '';

    /** @var string */
    public $last_run_end_at = '';
}
