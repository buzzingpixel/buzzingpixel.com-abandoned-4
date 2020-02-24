<?php

declare(strict_types=1);

namespace App\Software\Services;

use App\Payload\Payload;
use App\Persistence\SaveNewRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use App\Software\Models\SoftwareModel;
use App\Software\Transformers\TransformSoftwareModelToRecord;
use Exception;

/**
 * This should only be invoked from SaveSoftwareMaster where the PDO transaction
 * is begun and exception handling is in place
 */
class SaveNewSoftware
{
    private SaveNewRecord $saveNewRecord;
    private TransformSoftwareModelToRecord $transformer;
    private UuidFactoryWithOrderedTimeCodec $uuidFactory;

    public function __construct(
        SaveNewRecord $saveNewRecord,
        TransformSoftwareModelToRecord $transformer,
        UuidFactoryWithOrderedTimeCodec $uuidFactory
    ) {
        $this->saveNewRecord = $saveNewRecord;
        $this->transformer   = $transformer;
        $this->uuidFactory   = $uuidFactory;
    }

    /**
     * @throws Exception
     */
    public function __invoke(SoftwareModel $model) : void
    {
        $model->id = $this->uuidFactory->uuid1()->toString();

        $record = ($this->transformer)($model);

        $payload = ($this->saveNewRecord)($record);

        if ($payload->getStatus() === Payload::STATUS_CREATED) {
            return;
        }

        throw new Exception('Unknown error saving software');
    }
}
