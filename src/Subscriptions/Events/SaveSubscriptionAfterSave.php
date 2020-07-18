<?php

declare(strict_types=1);

namespace App\Subscriptions\Events;

use App\Events\StoppableEvent;
use App\Payload\Payload;
use App\Subscriptions\Models\SubscriptionModel;

class SaveSubscriptionAfterSave extends StoppableEvent
{
    public SubscriptionModel $subscriptionModel;
    public Payload $savePayload;

    public function __construct(
        SubscriptionModel $subscriptionModel,
        Payload $savePayload
    ) {
        $this->subscriptionModel = $subscriptionModel;
        $this->savePayload       = $savePayload;
    }
}
