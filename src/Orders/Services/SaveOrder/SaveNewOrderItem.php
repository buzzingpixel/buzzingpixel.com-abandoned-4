<?php

declare(strict_types=1);

namespace App\Orders\Services\SaveOrder;

use App\Orders\Models\OrderItemModel;
use App\Orders\Transformers\TransformOrderItemModelToRecord;
use App\Payload\Payload;
use App\Persistence\SaveNewRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use Exception;

/**
 * This should only be invoked from SaveOrderItemMaster, which is invoked from
 * SaveOrderMaster where the PDO transaction is begun and exception handling is
 * in place
 */
class SaveNewOrderItem
{
    private SaveNewRecord $saveNewRecord;
    private TransformOrderItemModelToRecord $transformer;
    private UuidFactoryWithOrderedTimeCodec $uuidFactory;

    public function __construct(
        SaveNewRecord $saveNewRecord,
        TransformOrderItemModelToRecord $transformer,
        UuidFactoryWithOrderedTimeCodec $uuidFactory
    ) {
        $this->saveNewRecord = $saveNewRecord;
        $this->transformer   = $transformer;
        $this->uuidFactory   = $uuidFactory;
    }

    /**
     * @throws Exception
     */
    public function __invoke(OrderItemModel $model) : void
    {
        $model->id = $this->uuidFactory->uuid1()->toString();

        $record = ($this->transformer)($model);

        $payload = ($this->saveNewRecord)($record);

        if ($payload->getStatus() === Payload::STATUS_CREATED) {
            return;
        }

        throw new Exception('Unknown error saving order item');
    }
}
