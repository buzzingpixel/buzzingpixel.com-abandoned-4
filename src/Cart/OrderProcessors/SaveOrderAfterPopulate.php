<?php

declare(strict_types=1);

namespace App\Cart\OrderProcessors;

use App\Cart\Models\ProcessOrderModel;
use App\Orders\Services\SaveOrder\SaveOrderMaster;
use App\Payload\Payload;
use Exception;
use Throwable;

class SaveOrderAfterPopulate
{
    private SaveOrderMaster $saveOrder;

    public function __construct(SaveOrderMaster $saveOrder)
    {
        $this->saveOrder = $saveOrder;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(
        ProcessOrderModel $processOrderModel
    ): ProcessOrderModel {
        $payload = ($this->saveOrder)(
            $processOrderModel->order(),
            $processOrderModel->order()->id,
        );

        if ($payload->getStatus() !== Payload::STATUS_CREATED) {
            throw new Exception('Something went wrong');
        }

        return $processOrderModel;
    }
}
