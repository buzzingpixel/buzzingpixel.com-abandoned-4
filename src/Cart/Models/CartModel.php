<?php

declare(strict_types=1);

namespace App\Cart\Models;

use App\Payload\Model;
use App\Software\Models\SoftwareModel;
use App\Users\Models\UserModel;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use function array_walk;
use function assert;

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

    private string $id = '';

    public function setId(string $id) : CartModel
    {
        $this->id = $id;

        return $this;
    }

    public function getId() : string
    {
        return $this->id;
    }

    private ?UserModel $user = null;

    public function setUser(?UserModel $user) : CartModel
    {
        $this->user = $user;

        return $this;
    }

    public function getUser() : ?UserModel
    {
        return $this->user;
    }

    private int $totalItems = 0;

    public function setTotalItems(int $totalItems) : CartModel
    {
        $this->totalItems = $totalItems;

        return $this;
    }

    public function getTotalItems() : int
    {
        return $this->totalItems;
    }

    private int $totalQuantity = 0;

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
    private array $items = [];

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

    /** @psalm-suppress PropertyNotSetInConstructor */
    private DateTimeImmutable $createdAt;

    protected function setCreatedAt(DateTimeImmutable $createdAt) : CartModel
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt() : DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return mixed[]
     */
    public function asArray(bool $excludeId = true) : array
    {
        $array = [];

        if (! $excludeId) {
            $array['id'] = $this->getId();
        }

        $array['user'] = null;

        $user = $this->getUser();

        if ($user !== null) {
            $array['user'] = $user->asArray($excludeId);
        }

        $array['totalItems'] = $this->getTotalItems();

        $array['totalQuantity'] = $this->getTotalQuantity();

        // TODO: generate items

        $array['createdAt'] = $this->getCreatedAt()->format(
            DateTimeInterface::ATOM
        );

        return $array;
    }

    public function calculateSubTotal() : float
    {
        $subTotal = 0;

        foreach ($this->getItems() as $item) {
            $itemSoftware = $item->getSoftware();
            assert($itemSoftware instanceof SoftwareModel);

            $subTotal = $itemSoftware->getPrice() * $item->getQuantity();
        }

        return (float) $subTotal;
    }

    public function calculateTax() : float
    {
        $user = $this->getUser();

        if ($user === null) {
            return 0.0;
        }

        // We only charge sales tax in TN
        if ($user->getBillingStateAbbr() !== 'TN') {
            return 0.0;
        }

        // TN tax is 7%
        return $this->calculateSubTotal() * 0.07;
    }

    public function calculateTotal() : float
    {
        return $this->calculateSubTotal() + $this->calculateTax();
    }

    public function canPurchase() : bool
    {
        $user = $this->getUser();

        if ($user === null) {
            return false;
        }

        return $user->getFirstName() !== '' &&
            $user->getLastName() !== '' &&
            $user->getBillingName() !== '' &&
            $user->getBillingCountry() !== '' &&
            $user->getBillingAddress() !== '' &&
            $user->getBillingCity() !== '' &&
            $user->getBillingPostalCode() !== '' &&
            $user->getBillingStateAbbr() !== '';
    }
}
