<?php

declare(strict_types=1);

namespace App\Software\Transformers;

use App\Persistence\Constants;
use App\Persistence\Software\SoftwareVersionRecord;
use App\Software\Models\SoftwareVersionModel;
use DateTimeImmutable;

class TransformSoftwareVersionRecordToModel
{
    public function __invoke(
        SoftwareVersionRecord $record
    ) : SoftwareVersionModel {
        return new SoftwareVersionModel([
            'id' => $record->id,
            'majorVersion' => $record->major_version,
            'version' => $record->version,
            'downloadFile' => $record->download_file,
            'releasedOn' => DateTimeImmutable::createFromFormat(
                Constants::POSTGRES_OUTPUT_FORMAT,
                $record->released_on
            ),
        ]);
    }
}
