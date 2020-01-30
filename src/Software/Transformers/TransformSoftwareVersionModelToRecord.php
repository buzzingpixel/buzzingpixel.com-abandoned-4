<?php

declare(strict_types=1);

namespace App\Software\Transformers;

use App\Persistence\Software\SoftwareVersionRecord;
use App\Software\Models\SoftwareVersionModel;
use DateTimeInterface;

class TransformSoftwareVersionModelToRecord
{
    public function __invoke(SoftwareVersionModel $model) : SoftwareVersionRecord
    {
        $record = new SoftwareVersionRecord();

        $record->id = $model->getId();

        $software = $model->getSoftware();

        if ($software !== null) {
            $record->software_id = $software->getId();
        }

        $record->major_version = $model->getMajorVersion();

        $record->version = $model->getVersion();

        $record->download_file = $model->getDownloadFile();

        $record->upgrade_price = (string) $model->getUpgradePrice();

        $record->released_on = $model->getReleasedOn()->format(
            DateTimeInterface::ATOM
        );

        return $record;
    }
}
