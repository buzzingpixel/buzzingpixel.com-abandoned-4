<?php

declare(strict_types=1);

namespace App\Software\Transformers;

use App\Persistence\Software\SoftwareRecord;
use App\Software\Models\SoftwareModel;
use App\Software\Models\SoftwareVersionModel;
use function in_array;

class TransformSoftwareRecordToModel
{
    /**
     * @param SoftwareVersionModel[] $versions
     */
    public function __invoke(
        SoftwareRecord $record,
        array $versions = []
    ) : SoftwareModel {
        return new SoftwareModel([
            'id' => $record->id,
            'slug' => $record->slug,
            'name' => $record->name,
            'isForSale' => in_array(
                $record->is_for_sale,
                ['1', 1, true],
                true
            ),
            'price' => (float) $record->price,
            'renewalPrice' => (float) $record->renewal_price,
            'isSubscription' => in_array(
                $record->is_subscription,
                ['1', 1, true],
                true
            ),
            'versions' => $versions,
        ]);
    }
}
