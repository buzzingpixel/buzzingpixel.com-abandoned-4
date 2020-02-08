<?php

declare(strict_types=1);

namespace App\Persistence\Schedule;

use App\Persistence\Record;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class ScheduleTrackingRecord extends Record
{
    protected static string $tableName = 'schedule_tracking';

    public string $class = '';

    /** @var string int|bool|string */
    public string $is_running = '0';

    public string $last_run_start_at = '';

    public string $last_run_end_at = '';
}
