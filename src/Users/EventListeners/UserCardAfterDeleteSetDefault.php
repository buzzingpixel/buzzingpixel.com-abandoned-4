<?php

declare(strict_types=1);

namespace App\Users\EventListeners;

use App\Users\Events\DeleteUserCardAfterDelete;
use App\Users\UserApi;

use function count;

class UserCardAfterDeleteSetDefault
{
    private UserApi $userApi;

    public function __construct(
        UserApi $userApi
    ) {
        $this->userApi = $userApi;
    }

    public function onAfterDeleteUserCard(
        DeleteUserCardAfterDelete $afterDelete
    ): void {
        $model = $afterDelete->userCardModel;

        // If this card was not the default card we can stop here
        if (! $model->isDefault) {
            return;
        }

        $userCards = $this->userApi->fetchUserCards($model->user);

        if (count($userCards) < 1) {
            return;
        }

        $card = $userCards[0];

        $card->isDefault = true;

        $this->userApi->saveUserCard($card);
    }
}
