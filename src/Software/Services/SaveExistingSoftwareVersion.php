<?php

declare(strict_types=1);

namespace App\Software\Services;

use App\Payload\Payload;
use App\Persistence\SaveExistingRecord;
use App\Software\Models\SoftwareVersionModel;
use App\Software\Transformers\TransformSoftwareVersionModelToRecord;
use Exception;

/**
 * This should only be invoked from SaveSoftwareVersionMaster where the PDO
 * transaction is begun and exception handling is in place
 */
class SaveExistingSoftwareVersion
{
    private SaveExistingRecord $saveExistingRecord;
    private TransformSoftwareVersionModelToRecord $transformer;

    public function __construct(
        SaveExistingRecord $saveExistingRecord,
        TransformSoftwareVersionModelToRecord $transformer
    ) {
        $this->saveExistingRecord = $saveExistingRecord;
        $this->transformer        = $transformer;
    }

    /**
     * @throws Exception
     */
    public function __invoke(SoftwareVersionModel $model): void
    {
        $record = ($this->transformer)($model);

        $payload = ($this->saveExistingRecord)($record);

        if ($payload->getStatus() === Payload::STATUS_UPDATED) {
            return;
        }

        throw new Exception('Unknown error saving software');
    }
}
