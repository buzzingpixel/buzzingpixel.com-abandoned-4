<?php

declare(strict_types=1);

namespace App\Orders\Models;

use App\Licenses\Models\LicenseModel;

class OrderItemModel
{
    public string $id = '';

    public OrderModel $order;

    public ?LicenseModel $license;

    public string $itemKey;
}
