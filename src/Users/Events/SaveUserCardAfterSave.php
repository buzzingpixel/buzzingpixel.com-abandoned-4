<?php

declare(strict_types=1);

namespace App\Users\Events;

use App\Events\StoppableEvent;
use App\Payload\Payload;
use App\Users\Models\UserCardModel;

class SaveUserCardAfterSave extends StoppableEvent
{
    public UserCardModel $userCardModel;
    public Payload $payload;

    public function __construct(
        UserCardModel $userCardModel,
        Payload $payload
    ) {
        $this->userCardModel = $userCardModel;
        $this->payload       = $payload;
    }
}
