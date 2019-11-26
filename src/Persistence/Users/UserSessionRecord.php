<?php

declare(strict_types=1);

namespace App\Persistence\Users;

use App\Persistence\Record;

class UserSessionRecord extends Record
{
    /** @var string */
    protected static $tableName = 'user_sessions';

    /** @var string */
    public $user_id = '';

    /** @var string */
    public $created_at = '';

    /** @var string */
    public $last_touched_at = '';
}
