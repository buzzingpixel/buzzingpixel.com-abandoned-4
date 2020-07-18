<?php

declare(strict_types=1);

namespace App\Users\Events;

use App\Events\StoppableEvent;
use App\Users\Models\UserModel;

class SaveUserBeforeSave extends StoppableEvent
{
    public UserModel $userModel;

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }
}
