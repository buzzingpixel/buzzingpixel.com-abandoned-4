<?php

declare(strict_types=1);

namespace App\Licenses\Services;

use App\Licenses\Models\LicenseModel;
use App\Licenses\Transformers\TransformLicenseModelToRecord;
use App\Payload\Payload;
use App\Persistence\SaveExistingRecord;
use Exception;

class SaveExistingLicense
{
    private SaveExistingRecord $saveExistingRecord;
    private TransformLicenseModelToRecord $transformer;

    public function __construct(
        SaveExistingRecord $saveExistingRecord,
        TransformLicenseModelToRecord $transformer
    ) {
        $this->saveExistingRecord = $saveExistingRecord;
        $this->transformer        = $transformer;
    }

    /**
     * @throws Exception
     */
    public function __invoke(LicenseModel $model) : void
    {
        $record = ($this->transformer)($model);

        $payload = ($this->saveExistingRecord)($record);

        if ($payload->getStatus() === Payload::STATUS_UPDATED) {
            return;
        }

        throw new Exception('Unknown error saving license');
    }
}
