<?php

declare(strict_types=1);

namespace App\Subscriptions\Events;

use App\Events\StoppableEvent;
use App\Subscriptions\Models\SubscriptionModel;

class SaveSubscriptionBeforeSave extends StoppableEvent
{
    public bool $isValid = true;
    /** @var string[] */
    public array $errors;
    public SubscriptionModel $subscriptionModel;

    /**
     * @param string[] $errors
     */
    public function __construct(
        array $errors,
        SubscriptionModel $subscriptionModel
    ) {
        $this->errors            = $errors;
        $this->subscriptionModel = $subscriptionModel;
    }
}
