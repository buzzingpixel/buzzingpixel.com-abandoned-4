<?php

declare(strict_types=1);

namespace App\Orders\Services\SaveOrder;

use App\Orders\Models\OrderItemModel;
use Exception;

/**
 * This should only be invoked from SaveOrderMaster where the PDO transaction
 * is begun and exception handling is in place
 */
class SaveOrderItemMaster
{
    private SaveNewOrderItem $saveNewOrderItem;
    private SaveExistingOrderItem $saveExistingOrderItem;

    public function __construct(
        SaveNewOrderItem $saveNewOrderItem,
        SaveExistingOrderItem $saveExistingOrderItem
    ) {
        $this->saveNewOrderItem      = $saveNewOrderItem;
        $this->saveExistingOrderItem = $saveExistingOrderItem;
    }

    /**
     * @throws Exception
     */
    public function __invoke(OrderItemModel $model) : void
    {
        if ($model->id === '') {
            ($this->saveNewOrderItem)($model);

            return;
        }

        ($this->saveExistingOrderItem)($model);
    }
}
