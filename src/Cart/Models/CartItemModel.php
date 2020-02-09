<?php

declare(strict_types=1);

namespace App\Cart\Models;

use App\Software\Models\SoftwareModel;

class CartItemModel
{
    public string $id = '';

    public ?CartModel $cart = null;

    public ?SoftwareModel $software = null;

    public int $quantity = 0;
}
