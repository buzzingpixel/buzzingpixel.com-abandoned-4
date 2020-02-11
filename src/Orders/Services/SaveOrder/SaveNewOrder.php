<?php

declare(strict_types=1);

namespace App\Orders\Services\SaveOrder;

use App\Orders\Models\OrderModel;
use App\Orders\Transformers\TransformOrderModelToRecord;
use App\Payload\Payload;
use App\Persistence\SaveNewRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use Exception;

/**
 * This should only be invoked from SaveOrderMaster where the PDO transaction
 * is begun and exception handling is in place
 */
class SaveNewOrder
{
    private SaveNewRecord $saveNewRecord;
    private TransformOrderModelToRecord $transformer;
    private UuidFactoryWithOrderedTimeCodec $uuidFactory;

    public function __construct(
        SaveNewRecord $saveNewRecord,
        TransformOrderModelToRecord $transformer,
        UuidFactoryWithOrderedTimeCodec $uuidFactory
    ) {
        $this->saveNewRecord = $saveNewRecord;
        $this->transformer   = $transformer;
        $this->uuidFactory   = $uuidFactory;
    }

    /**
     * @throws Exception
     */
    public function __invoke(OrderModel $model) : void
    {
        $model->id = $this->uuidFactory->uuid1()->toString();

        $record = ($this->transformer)($model);

        $payload = ($this->saveNewRecord)($record);

        if ($payload->getStatus() === Payload::STATUS_CREATED) {
            return;
        }

        throw new Exception('Unknown error saving order');
    }
}
