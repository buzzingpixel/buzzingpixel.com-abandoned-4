<?php

declare(strict_types=1);

namespace App\Orders\Models;

use App\Licenses\Models\LicenseModel;
use DateTimeImmutable;

class OrderItemModel
{
    public string $id = '';

    public OrderModel $order;

    public LicenseModel $license;

    public string $itemKey = '';

    public string $itemTitle = '';

    public string $majorVersion = '';

    public string $version = '';

    public float $price = 0.0;

    public float $originalPrice = 0.0;

    public bool $isUpgrade = false;

    public bool $hasBeenUpgraded = false;

    public ?DateTimeImmutable $expires = null;
}
