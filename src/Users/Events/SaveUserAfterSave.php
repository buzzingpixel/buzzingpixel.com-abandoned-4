<?php

declare(strict_types=1);

namespace App\Users\Events;

use App\Events\StoppableEvent;
use App\Payload\Payload;
use App\Users\Models\UserModel;

class SaveUserAfterSave extends StoppableEvent
{
    public UserModel $userModel;
    public Payload $payload;

    public function __construct(UserModel $userModel, Payload $payload)
    {
        $this->userModel = $userModel;
        $this->payload   = $payload;
    }
}
