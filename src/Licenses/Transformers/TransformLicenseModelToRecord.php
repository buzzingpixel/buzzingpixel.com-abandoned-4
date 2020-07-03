<?php

declare(strict_types=1);

namespace App\Licenses\Transformers;

use App\Licenses\Models\LicenseModel;
use App\Persistence\Licenses\LicenseRecord;
use DateTimeInterface;

use function Safe\json_encode;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformLicenseModelToRecord
{
    public function __invoke(LicenseModel $model): LicenseRecord
    {
        $record = new LicenseRecord();

        $record->id = $model->id;

        if ($model->ownerUser !== null) {
            $record->owner_user_id = $model->ownerUser->id;
        }

        $record->item_key = $model->itemKey;

        $record->item_title = $model->itemTitle;

        $record->major_version = $model->majorVersion;

        $record->version = $model->version;

        $record->last_available_version = $model->lastAvailableVersion;

        $record->notes = $model->notes;

        /** @noinspection PhpUnhandledExceptionInspection */
        $record->authorized_domains = json_encode(
            $model->authorizedDomains
        );

        $record->is_disabled = $model->isDisabled ? '1' : '0';

        if ($model->expires !== null) {
            $record->expires = $model->expires->format(
                DateTimeInterface::ATOM
            );
        } else {
            $record->expires = null;
        }

        return $record;
    }
}
