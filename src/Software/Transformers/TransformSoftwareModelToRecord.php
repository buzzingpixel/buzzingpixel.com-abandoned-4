<?php

declare(strict_types=1);

namespace App\Software\Transformers;

use App\Persistence\Software\SoftwareRecord;
use App\Software\Models\SoftwareModel;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformSoftwareModelToRecord
{
    public function __invoke(SoftwareModel $model): SoftwareRecord
    {
        $record = new SoftwareRecord();

        $record->id = $model->id;

        $record->slug = $model->slug;

        $record->name = $model->name;

        $record->is_for_sale = $model->isForSale ? '1' : '0';

        $record->price = (string) $model->price;

        $record->renewal_price = (string) $model->renewalPrice;

        $record->is_subscription = $model->isSubscription ? '1' : '0';

        return $record;
    }
}
