<?php

declare(strict_types=1);

namespace App\Orders\Services\SaveOrder;

use App\Orders\Models\OrderItemModel;
use App\Orders\Transformers\TransformOrderItemModelToRecord;
use App\Payload\Payload;
use App\Persistence\SaveExistingRecord;
use Exception;

/**
 * This should only be invoked from SaveOrderItemMaster, which is invoked from
 * SaveOrderMaster where the PDO transaction is begun and exception handling is
 * in place
 */
class SaveExistingOrderItem
{
    private SaveExistingRecord $saveNewRecord;
    private TransformOrderItemModelToRecord $transformer;

    public function __construct(
        SaveExistingRecord $saveNewRecord,
        TransformOrderItemModelToRecord $transformer
    ) {
        $this->saveNewRecord = $saveNewRecord;
        $this->transformer   = $transformer;
    }

    /**
     * @throws Exception
     */
    public function __invoke(OrderItemModel $model): void
    {
        $record = ($this->transformer)($model);

        $payload = ($this->saveNewRecord)($record);

        if ($payload->getStatus() === Payload::STATUS_UPDATED) {
            return;
        }

        throw new Exception('Unknown error saving order item');
    }
}
