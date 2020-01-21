<?php

declare(strict_types=1);

namespace App\Cart\Models;

use App\Software\Models\SoftwareModel;

class CartItemModel
{
    /** @var string */
    private $id = '';

    public function setId(string $id) : CartItemModel
    {
        $this->id = $id;

        return $this;
    }

    public function getId() : string
    {
        return $this->id;
    }

    /** @var CartModel|null */
    private $cart;

    public function setCart(?CartModel $cart) : CartItemModel
    {
        $this->cart = $cart;

        return $this;
    }

    public function getCart() : ?CartModel
    {
        return $this->cart;
    }

    /** @var SoftwareModel|null */
    private $software;

    public function setSoftware(?SoftwareModel $software) : CartItemModel
    {
        $this->software = $software;

        return $this;
    }

    public function getSoftware() : ?SoftwareModel
    {
        return $this->software;
    }

    /** @var int */
    private $quantity = 0;

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
