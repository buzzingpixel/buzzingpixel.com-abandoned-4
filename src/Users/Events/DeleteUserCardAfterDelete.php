<?php

declare(strict_types=1);

namespace App\Users\Events;

use App\Events\StoppableEvent;
use App\Users\Models\UserCardModel;

class DeleteUserCardAfterDelete extends StoppableEvent
{
    public UserCardModel $userCardModel;
    public bool $isValid = true;

    public function __construct(
        UserCardModel $userCardModel
    ) {
        $this->userCardModel = $userCardModel;
    }
}
