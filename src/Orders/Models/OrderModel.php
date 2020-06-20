<?php

declare(strict_types=1);

namespace App\Orders\Models;

use App\Users\Models\UserModel;
use DateTimeZone;
use RuntimeException;
use Safe\DateTimeImmutable;

use function assert;
use function is_array;

/**
 * @property OrderItemModel[] $items
 */
class OrderModel
{
    public function __construct()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->date = new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC')
        );
    }

    public string $id = '';

    public int $oldOrderNumber = 0;

    public ?UserModel $user = null;

    public string $stripeId = '';

    public float $stripeAmount = 0.0;

    public string $stripeBalanceTransaction = '';

    public bool $stripeCaptured = false;

    public int $stripeCreated = 0;

    public string $stripeCurrency = '';

    public bool $stripePaid = false;

    public float $subtotal = 0.0;

    public float $tax = 0.0;

    public float $total = 0.0;

    public string $name = '';

    public string $company = '';

    public string $phoneNumber = '';

    public string $country = '';

    public string $address = '';

    public string $addressContinued = '';

    public string $city = '';

    public string $state = '';

    public string $postalCode = '';

    public DateTimeImmutable $date;

    /** @var OrderItemModel[] */
    private array $items = [];

    public function addItem(OrderItemModel $item): void
    {
        $item->order = $this;

        $this->items[] = $item;
    }

    /**
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        if ($name !== 'items') {
            throw new RuntimeException('Property does not exist');
        }

        assert(is_array($value));

        /** @psalm-suppress MixedAssignment */
        foreach ($value as $item) {
            assert($item instanceof OrderItemModel);

            $this->addItem($item);
        }
    }

    public function __isset(string $name): bool
    {
        return $name === 'items';
    }

    /**
     * @return mixed
     */
    public function __get(string $name)
    {
        if ($name !== 'items') {
            throw new RuntimeException('Property does not exist');
        }

        return $this->items;
    }
}
