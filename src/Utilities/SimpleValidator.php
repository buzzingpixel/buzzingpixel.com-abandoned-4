<?php

declare(strict_types=1);

namespace App\Utilities;

use App\Users\Services\SaveUser;
use const FILTER_VALIDATE_EMAIL;
use function filter_var;
use function mb_strlen;

class SimpleValidator
{
    public static function email(string $email) : bool
    {
        $const = FILTER_VALIDATE_EMAIL;

        return filter_var($email, $const) !== false;
    }

    public static function password(string $pass) : bool
    {
        return mb_strlen($pass) === SaveUser::MIN_PASSWORD_LENGTH;
    }
}
