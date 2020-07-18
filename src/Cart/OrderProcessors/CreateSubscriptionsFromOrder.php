<?php

declare(strict_types=1);

namespace App\Cart\OrderProcessors;

use App\Cart\Models\ProcessOrderModel;
use App\Orders\Models\OrderItemModel;
use App\Software\Models\SoftwareModel;
use App\Software\SoftwareApi;
use App\Subscriptions\Models\SubscriptionModel;
use App\Subscriptions\SubscriptionApi;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;

use function assert;

class CreateSubscriptionsFromOrder
{
    private SubscriptionApi $subscriptionApi;

    /** @var array<string, SoftwareModel> */
    private array $software = [];

    public function __construct(
        SubscriptionApi $subscriptionApi,
        SoftwareApi $softwareApi
    ) {
        $this->subscriptionApi = $subscriptionApi;

        foreach ($softwareApi->fetchAllSoftware() as $software) {
            $this->software[$software->slug] = $software;
        }
    }

    public function __invoke(
        ProcessOrderModel $processOrderModel
    ): ProcessOrderModel {
        $orderItems = $processOrderModel->order()->items;

        $user = $processOrderModel->cart()->user;

        assert($user instanceof UserModel);

        foreach ($orderItems as $orderItem) {
            $this->processOrderItem(
                $orderItem,
                $user,
                $processOrderModel->card()
            );
        }

        return $processOrderModel;
    }

    private function processOrderItem(
        OrderItemModel $orderItem,
        UserModel $user,
        UserCardModel $card
    ): void {
        $software = $this->software[$orderItem->itemKey];

        if (! $software->isSubscription) {
            return;
        }

        $subscription = new SubscriptionModel();

        $subscription->user = $user;

        $subscription->license = $orderItem->license;

        $subscription->addOrder($orderItem->order);

        $subscription->card = $card;

        $this->subscriptionApi->saveSubscription($subscription);
    }
}
