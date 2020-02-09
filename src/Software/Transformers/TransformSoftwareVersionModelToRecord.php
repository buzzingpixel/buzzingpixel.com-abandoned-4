<?php

declare(strict_types=1);

namespace App\Software\Transformers;

use App\Persistence\Software\SoftwareVersionRecord;
use App\Software\Models\SoftwareVersionModel;
use DateTimeInterface;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformSoftwareVersionModelToRecord
{
    public function __invoke(SoftwareVersionModel $model) : SoftwareVersionRecord
    {
        $record = new SoftwareVersionRecord();

        $record->id = $model->id;

        if ($model->software !== null) {
            $record->software_id = $model->software->id;
        }

        $record->major_version = $model->majorVersion;

        $record->version = $model->version;

        $record->download_file = $model->downloadFile;

        $record->upgrade_price = (string) $model->upgradePrice;

        $record->released_on = $model->releasedOn->format(
            DateTimeInterface::ATOM
        );

        return $record;
    }
}
