<?php

declare(strict_types=1);

namespace App\Software\Transformers;

use App\Persistence\Constants;
use App\Persistence\Software\SoftwareVersionRecord;
use App\Software\Models\SoftwareVersionModel;
use Safe\DateTimeImmutable;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformSoftwareVersionRecordToModel
{
    public function __invoke(
        SoftwareVersionRecord $record
    ): SoftwareVersionModel {
        $model = new SoftwareVersionModel();

        $model->id = $record->id;

        $model->majorVersion = $record->major_version;

        $model->version = $record->version;

        $model->downloadFile = $record->download_file;

        $model->upgradePrice = (float) $record->upgrade_price;

        $releasedOn = DateTimeImmutable::createFromFormat(
            Constants::POSTGRES_OUTPUT_FORMAT,
            $record->released_on
        );

        $model->releasedOn = $releasedOn;

        return $model;
    }
}
