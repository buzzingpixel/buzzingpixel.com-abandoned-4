<?php

declare(strict_types=1);

namespace App\Software\Services;

use App\Payload\Payload;
use App\Persistence\SaveExistingRecord;
use App\Software\Models\SoftwareModel;
use App\Software\Transformers\TransformSoftwareModelToRecord;
use Exception;

/**
 * This should only be invoked from SaveSoftwareMaster where the PDO transaction
 * is begun and exception handling is in place
 */
class SaveExistingSoftware
{
    private SaveExistingRecord $saveExistingRecord;
    private TransformSoftwareModelToRecord $transformer;

    public function __construct(
        SaveExistingRecord $saveExistingRecord,
        TransformSoftwareModelToRecord $transformer
    ) {
        $this->saveExistingRecord = $saveExistingRecord;
        $this->transformer        = $transformer;
    }

    /**
     * @throws Exception
     */
    public function __invoke(SoftwareModel $model) : void
    {
        $record = ($this->transformer)($model);

        $payload = ($this->saveExistingRecord)($record);

        if ($payload->getStatus() === Payload::STATUS_UPDATED) {
            return;
        }

        throw new Exception('Unknown error saving software');
    }
}
