<?php

declare(strict_types=1);

namespace App\Cart\Models;

use App\Software\Models\SoftwareModel;
use App\Users\Models\UserModel;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use RuntimeException;
use function assert;
use function is_array;

/**
 * @property CartItemModel[] $items
 */
class CartModel
{
    public function __construct()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->createdAt = new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC')
        );
    }

    /**
     * @param mixed $value
     */
    public function __set(string $name, $value) : void
    {
        if ($name !== 'items') {
            throw new RuntimeException('Invalid property');
        }

        assert(is_array($value));

        foreach ($value as $item) {
            $this->addItem($item);
        }
    }

    public function __isset(string $name) : bool
    {
        return $name === 'items';
    }

    /**
     * @return mixed
     */
    public function __get(string $name)
    {
        if ($name !== 'items') {
            throw new RuntimeException('Invalid property');
        }

        return $this->items;
    }

    public string $id = '';

    public ?UserModel $user = null;

    public int $totalItems = 0;

    public int $totalQuantity = 0;

    /** @var CartItemModel[] */
    private array $items = [];

    public function addItem(CartItemModel $item) : CartModel
    {
        $item->setCart($this);

        $this->items[] = $item;

        return $this;
    }

    public DateTimeImmutable $createdAt;

    /**
     * @return mixed[]
     */
    public function asArray(bool $excludeId = true) : array
    {
        $array = [];

        if (! $excludeId) {
            $array['id'] = $this->id;
        }

        $array['user'] = null;

        if ($this->user !== null) {
            $array['user'] = $this->user->asArray($excludeId);
        }

        $array['totalItems'] = $this->totalItems;

        $array['totalQuantity'] = $this->totalQuantity;

        // TODO: generate items

        $array['createdAt'] = $this->createdAt->format(
            DateTimeInterface::ATOM
        );

        return $array;
    }

    public function calculateSubTotal() : float
    {
        $subTotal = 0;

        foreach ($this->items as $item) {
            $itemSoftware = $item->getSoftware();

            assert($itemSoftware instanceof SoftwareModel);

            $subTotal = $itemSoftware->price * $item->getQuantity();
        }

        return (float) $subTotal;
    }

    public function calculateTax() : float
    {
        $user = $this->user;

        if ($user === null) {
            return 0.0;
        }

        // We only charge sales tax in TN
        if ($user->billingStateAbbr !== 'TN') {
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
        $user = $this->user;

        if ($user === null) {
            return false;
        }

        return $user->firstName !== '' &&
            $user->lastName !== '' &&
            $user->billingName !== '' &&
            $user->billingCountry !== '' &&
            $user->billingAddress !== '' &&
            $user->billingCity !== '' &&
            $user->billingPostalCode !== '' &&
            $user->billingStateAbbr !== '';
    }
}
