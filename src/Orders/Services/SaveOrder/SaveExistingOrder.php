<?php

declare(strict_types=1);

namespace App\Orders\Services\SaveOrder;

use App\Orders\Models\OrderModel;
use App\Orders\Transformers\TransformOrderModelToRecord;
use App\Payload\Payload;
use App\Persistence\SaveExistingRecord;
use Exception;

/**
 * This should only be invoked from SaveOrderMaster where the PDO transaction
 * is begun and exception handling is in place
 */
class SaveExistingOrder
{
    private SaveExistingRecord $saveExistingRecord;
    private TransformOrderModelToRecord $transformer;

    public function __construct(
        SaveExistingRecord $saveExistingRecord,
        TransformOrderModelToRecord $transformer
    ) {
        $this->saveExistingRecord = $saveExistingRecord;
        $this->transformer        = $transformer;
    }

    /**
     * @throws Exception
     */
    public function __invoke(OrderModel $model): void
    {
        $record = ($this->transformer)($model);

        $payload = ($this->saveExistingRecord)($record);

        if ($payload->getStatus() === Payload::STATUS_UPDATED) {
            return;
        }

        throw new Exception('Unknown error saving order');
    }
}
