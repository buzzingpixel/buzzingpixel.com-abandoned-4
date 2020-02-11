<?php

declare(strict_types=1);

namespace App\Orders\Services\SaveOrder;

use App\Orders\Models\OrderModel;
use App\Payload\Payload;
use PDO;
use Throwable;
use function array_walk;

class SaveOrderMaster
{
    private PDO $pdo;
    private SaveNewOrder $saveNewOrder;
    private SaveExistingOrder $saveExistingOrder;
    private SaveOrderItemMaster $saveOrderItemMaster;

    public function __construct(
        PDO $pdo,
        SaveNewOrder $saveNewOrder,
        SaveExistingOrder $saveExistingOrder,
        SaveOrderItemMaster $saveOrderItemMaster
    ) {
        $this->pdo                 = $pdo;
        $this->saveNewOrder        = $saveNewOrder;
        $this->saveExistingOrder   = $saveExistingOrder;
        $this->saveOrderItemMaster = $saveOrderItemMaster;
    }

    public function __invoke(OrderModel $order) : Payload
    {
        try {
            $this->pdo->beginTransaction();

            if ($order->id === '') {
                ($this->saveNewOrder)($order);

                $items = $order->items;

                array_walk(
                    $items,
                    $this->saveOrderItemMaster
                );

                $this->pdo->commit();

                return new Payload(Payload::STATUS_CREATED);
            }

            ($this->saveExistingOrder)($order);

            $items = $order->items;

            array_walk(
                $items,
                $this->saveOrderItemMaster
            );

            $this->pdo->commit();

            return new Payload(Payload::STATUS_UPDATED);
        } catch (Throwable $e) {
            $this->pdo->rollBack();

            return new Payload(
                Payload::STATUS_ERROR,
                ['message' => 'An unknown error occurred']
            );
        }
    }
}
