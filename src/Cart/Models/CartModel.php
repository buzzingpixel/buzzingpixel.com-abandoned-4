<?php

declare(strict_types=1);

namespace App\Cart\Models;

use App\Payload\Model;
use App\Users\Models\UserModel;
use function array_walk;

class CartModel extends Model
{
    /** @var string */
    private $id = '';

    public function setId(string $id) : CartModel
    {
        $this->id = $id;

        return $this;
    }

    public function getId() : string
    {
        return $this->id;
    }

    /** @var UserModel|null */
    private $user;

    public function setUser(?UserModel $user) : CartModel
    {
        $this->user = $user;

        return $this;
    }

    public function getUser() : ?UserModel
    {
        return $this->user;
    }

    /** @var int */
    private $totalItems = 0;

    public function setTotalItems(int $totalItems) : CartModel
    {
        $this->totalItems = $totalItems;

        return $this;
    }

    public function getTotalItems() : int
    {
        return $this->totalItems;
    }

    /** @var int */
    private $totalQuantity = 0;

    public function setTotalQuantity(int $totalQuantity) : CartModel
    {
        $this->totalQuantity = $totalQuantity;

        return $this;
    }

    public function getTotalQuantity() : int
    {
        return $this->totalQuantity;
    }

    /** @var CartItemModel */
    private $items = [];

    /**
     * @param CartItemModel[] $items
     */
    public function setItems(array $items) : CartModel
    {
        array_walk($items, [$this, 'addItem']);

        return $this;
    }

    public function addItem(CartItemModel $item) : CartModel
    {
        $item->setCart($this);

        $this->items[] = $item;

        return $this;
    }

    public function getItems() : CartItemModel
    {
        return $this->items;
    }
}
