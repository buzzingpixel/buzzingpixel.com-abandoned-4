<?php

declare(strict_types=1);

namespace App\Orders\Transformers;

use App\Licenses\LicenseApi;
use App\Licenses\Models\LicenseModel;
use App\Orders\Models\OrderItemModel;
use App\Persistence\Orders\OrderItemRecord;

use function assert;
use function in_array;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformOrderItemRecordToModel
{
    private LicenseApi $licenseApi;

    public function __construct(LicenseApi $licenseApi)
    {
        $this->licenseApi = $licenseApi;
    }

    public function __invoke(
        OrderItemRecord $record,
        ?LicenseModel $license = null
    ): OrderItemModel {
        $model = new OrderItemModel();

        $model->id = $record->id;

        if ($license === null || $license->id !== $record->license_id) {
            $license = $this->licenseApi->fetchLicenseById(
                $record->license_id
            );
        }

        assert($license instanceof LicenseModel);

        $model->license = $license;

        $model->itemKey = $record->item_key;

        $model->itemTitle = $record->item_title;

        $model->majorVersion = $record->major_version;

        $model->version = $record->version;

        $model->price = (float) $record->price;

        $model->originalPrice = (float) $record->original_price;

        $model->isUpgrade = in_array(
            $record->is_upgrade,
            ['1', 1, 'true', true],
            true
        );

        $model->hasBeenUpgraded = in_array(
            $record->has_been_upgraded,
            ['1', 1, 'true', true],
            true
        );

        return $model;
    }
}
