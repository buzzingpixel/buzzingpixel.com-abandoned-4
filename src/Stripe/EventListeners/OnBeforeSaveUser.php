<?php

declare(strict_types=1);

namespace App\Stripe\EventListeners;

use App\Stripe\Services\UpdateStripeCustomer;
use App\Users\Events\SaveUserBeforeSave;

class OnBeforeSaveUser
{
    private UpdateStripeCustomer $updateStripeCustomer;

    public function __construct(UpdateStripeCustomer $updateStripeCustomer)
    {
        $this->updateStripeCustomer = $updateStripeCustomer;
    }

    public function onBeforeSaveUser(SaveUserBeforeSave $beforeSave): void
    {
        ($this->updateStripeCustomer)($beforeSave->userModel);
    }
}
