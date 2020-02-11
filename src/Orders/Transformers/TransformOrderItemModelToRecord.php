<?php

declare(strict_types=1);

namespace App\Orders\Transformers;

use App\Orders\Models\OrderItemModel;
use App\Persistence\Orders\OrderItemRecord;
use DateTimeInterface;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformOrderItemModelToRecord
{
    public function __invoke(OrderItemModel $model) : OrderItemRecord
    {
        $record = new OrderItemRecord();

        $record->id = $model->id;

        $record->order_id = $model->order->id;

        $record->license_id = $model->license->id;

        $record->item_key = $model->itemKey;

        $record->item_title = $model->itemTitle;

        $record->major_version = $model->majorVersion;

        $record->version = $model->version;

        $record->price = $model->price;

        $record->original_price = $model->originalPrice;

        $record->is_upgrade = $model->isUpgrade ? '1' : '0';

        $record->has_been_upgraded = $model->hasBeenUpgraded ? '1' : '0';

        if ($model->expires !== null) {
            $record->expires = $model->expires->format(
                DateTimeInterface::ATOM
            );
        }

        return $record;
    }
}
