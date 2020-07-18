<?php

declare(strict_types=1);

namespace App\Licenses\Services;

use App\Licenses\Models\LicenseModel;
use App\Licenses\Transformers\TransformLicenseModelToRecord;
use App\Payload\Payload;
use App\Persistence\SaveNewRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use Exception;

/**
 * This should only be invoked from SaveLicenseMaster where the PDO transaction
 * is begun and exception handling is in place
 */
class SaveNewLicense
{
    private SaveNewRecord $saveNewRecord;
    private TransformLicenseModelToRecord $transformer;
    private UuidFactoryWithOrderedTimeCodec $uuidFactory;

    public function __construct(
        SaveNewRecord $saveNewRecord,
        TransformLicenseModelToRecord $transformer,
        UuidFactoryWithOrderedTimeCodec $uuidFactory
    ) {
        $this->saveNewRecord = $saveNewRecord;
        $this->transformer   = $transformer;
        $this->uuidFactory   = $uuidFactory;
    }

    /**
     * @throws Exception
     */
    public function __invoke(LicenseModel $model): void
    {
        $model->id = $this->uuidFactory->uuid1()->toString();

        $record = ($this->transformer)($model);

        $payload = ($this->saveNewRecord)($record);

        if ($payload->getStatus() === Payload::STATUS_CREATED) {
            return;
        }

        throw new Exception('Unknown error saving license');
    }
}
