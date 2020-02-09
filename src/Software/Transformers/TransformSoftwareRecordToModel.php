<?php

declare(strict_types=1);

namespace App\Software\Transformers;

use App\Persistence\Software\SoftwareRecord;
use App\Software\Models\SoftwareModel;
use App\Software\Models\SoftwareVersionModel;
use function in_array;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformSoftwareRecordToModel
{
    /**
     * @param SoftwareVersionModel[] $versions
     */
    public function __invoke(
        SoftwareRecord $record,
        array $versions = []
    ) : SoftwareModel {
        $model = new SoftwareModel();

        $model->id = $record->id;

        $model->slug = $record->slug;

        $model->name = $record->name;

        $model->isForSale = in_array(
            $record->is_for_sale,
            ['1', 1, true],
            true
        );

        $model->price = (float) $record->price;

        $model->renewalPrice = (float) $record->renewal_price;

        $model->isSubscription = in_array(
            $record->is_subscription,
            ['1', 1, true],
            true
        );

        $model->versions = $versions;

        return $model;
    }
}
