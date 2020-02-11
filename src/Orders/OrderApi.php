<?php

declare(strict_types=1);

namespace App\Orders;

use App\Orders\Models\OrderModel;
use App\Orders\Services\SaveOrder\SaveOrderMaster;
use App\Payload\Payload;
use Psr\Container\ContainerInterface;
use function assert;

class OrderApi
{
    private ContainerInterface $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function saveOrder(OrderModel $orderModel) : Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(SaveOrderMaster::class);

        assert($service instanceof SaveOrderMaster);

        return $service($orderModel);
    }
}
