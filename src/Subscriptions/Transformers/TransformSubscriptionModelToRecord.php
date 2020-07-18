<?php

declare(strict_types=1);

namespace App\Subscriptions\Transformers;

use App\Orders\Models\OrderModel;
use App\Persistence\Subscriptions\SubscriptionRecord;
use App\Subscriptions\Models\SubscriptionModel;

use function array_map;
use function Safe\json_encode;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformSubscriptionModelToRecord
{
    public function __invoke(SubscriptionModel $model): SubscriptionRecord
    {
        $record = new SubscriptionRecord();

        $record->id = $model->id;

        $record->user_id = $model->user->id;

        $record->license_id = $model->license->id;

        $record->order_ids = json_encode(array_map(
            static fn (OrderModel $order) => $order->id,
            $model->orders,
        ));

        $record->auto_renew = $model->autoRenew ? '1' : '0';

        $record->card_id = $model->card->id ?? null;

        return $record;
    }
}
