<?php

declare(strict_types=1);

namespace App\Orders\Services\SaveOrder;

use App\Licenses\Services\SaveLicenseMaster;
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
    private SaveLicenseMaster $saveLicenseMaster;

    public function __construct(
        SaveNewOrderItem $saveNewOrderItem,
        SaveExistingOrderItem $saveExistingOrderItem,
        SaveLicenseMaster $saveLicenseMaster
    ) {
        $this->saveNewOrderItem      = $saveNewOrderItem;
        $this->saveExistingOrderItem = $saveExistingOrderItem;
        $this->saveLicenseMaster     = $saveLicenseMaster;
    }

    /**
     * @throws Exception
     */
    public function __invoke(OrderItemModel $model): void
    {
        ($this->saveLicenseMaster)($model->license);

        if ($model->id === '') {
            ($this->saveNewOrderItem)($model);

            return;
        }

        ($this->saveExistingOrderItem)($model);
    }
}
