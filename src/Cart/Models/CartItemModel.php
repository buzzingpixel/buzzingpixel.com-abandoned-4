<?php

declare(strict_types=1);

namespace App\Cart\Models;

use App\Payload\Model;
use App\Software\Models\SoftwareModel;

class CartItemModel extends Model
{
    private string $id = '';

    public function setId(string $id) : CartItemModel
    {
        $this->id = $id;

        return $this;
    }

    public function getId() : string
    {
        return $this->id;
    }

    private ?CartModel $cart = null;

    public function setCart(?CartModel $cart) : CartItemModel
    {
        $this->cart = $cart;

        return $this;
    }

    public function getCart() : ?CartModel
    {
        return $this->cart;
    }

    private ?SoftwareModel $software = null;

    public function setSoftware(?SoftwareModel $software) : CartItemModel
    {
        $this->software = $software;

        return $this;
    }

    public function getSoftware() : ?SoftwareModel
    {
        return $this->software;
    }

    private int $quantity = 0;

    public function setQuantity(int $quantity) : CartItemModel
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getQuantity() : int
    {
        return $this->quantity;
    }
}
