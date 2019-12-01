<?php

declare(strict_types=1);

namespace App\Persistence\Users;

use App\Persistence\Record;

class UserPasswordResetTokenRecord extends Record
{
    /** @var string */
    protected static $tableName = 'user_password_reset_tokens';

    /** @var string */
    public $user_id = '';

    /** @var string */
    public $created_at = '';
}
