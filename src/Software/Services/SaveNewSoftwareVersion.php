<?php

declare(strict_types=1);

namespace App\Software\Services;

use App\Payload\Payload;
use App\Persistence\SaveNewRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use App\Software\Models\SoftwareVersionModel;
use App\Software\Transformers\TransformSoftwareVersionModelToRecord;
use Exception;

/**
 * This should only be invoked from SaveSoftwareVersionMaster where the PDO
 * transaction is begun and exception handling is in place
 */
class SaveNewSoftwareVersion
{
    private SaveNewRecord $saveNewRecord;
    private TransformSoftwareVersionModelToRecord $transformer;
    private UuidFactoryWithOrderedTimeCodec $uuidFactory;

    public function __construct(
        SaveNewRecord $saveNewRecord,
        TransformSoftwareVersionModelToRecord $transformer,
        UuidFactoryWithOrderedTimeCodec $uuidFactory
    ) {
        $this->saveNewRecord = $saveNewRecord;
        $this->transformer   = $transformer;
        $this->uuidFactory   = $uuidFactory;
    }

    /**
     * @throws Exception
     */
    public function __invoke(SoftwareVersionModel $model): void
    {
        $model->id = $this->uuidFactory->uuid1()->toString();

        $record = ($this->transformer)($model);

        $payload = ($this->saveNewRecord)($record);

        if ($payload->getStatus() === Payload::STATUS_CREATED) {
            return;
        }

        throw new Exception('Unknown error saving software version');
    }
}
