<?php

declare(strict_types=1);

namespace App\Cart\Models;

use App\Payload\Model;
use App\Users\Models\UserModel;
use DateTimeImmutable;
use DateTimeZone;
use function array_walk;

class CartModel extends Model
{
    /**
     * @inheritDoc
     */
    public function __construct(array $vars = [])
    {
        parent::__construct($vars);

        /** @psalm-suppress UninitializedProperty */
        $createdAtInstance = $this->createdAt instanceof DateTimeImmutable;

        if ($createdAtInstance) {
            return;
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->createdAt = new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC')
        );
    }

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

    /** @var CartItemModel[] */
    private $items = [];

    /**
     * @param CartItemModel[] $items
     */
    public function setItems(array $items) : CartModel
    {
        $this->items = [];

        array_walk($items, [$this, 'addItem']);

        return $this;
    }

    public function addItem(CartItemModel $item) : CartModel
    {
        $item->setCart($this);

        $this->items[] = $item;

        return $this;
    }

    /**
     * @return CartItemModel[]
     */
    public function getItems() : array
    {
        return $this->items;
    }

    /**
     * @var DateTimeImmutable
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $createdAt;

    protected function setCreatedAt(DateTimeImmutable $createdAt) : CartModel
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt() : DateTimeImmutable
    {
        return $this->createdAt;
    }
}
