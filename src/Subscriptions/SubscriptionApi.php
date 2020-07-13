<?php

declare(strict_types=1);

namespace App\Subscriptions;

use App\Payload\Payload;
use App\Subscriptions\Models\SubscriptionModel;
use App\Subscriptions\Services\SaveSubscription;
use Psr\Container\ContainerInterface;

use function assert;

class SubscriptionApi
{
    private ContainerInterface $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function saveSubscription(SubscriptionModel $model): Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(SaveSubscription::class);

        assert($service instanceof SaveSubscription);

        return $service($model);
    }
}
