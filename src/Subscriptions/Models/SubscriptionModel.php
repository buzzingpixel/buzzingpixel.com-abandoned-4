<?php

declare(strict_types=1);

namespace App\Subscriptions\Models;

use App\Licenses\Models\LicenseModel;
use App\Orders\Models\OrderModel;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use RuntimeException;

use function array_walk;
use function assert;
use function is_array;

/**
 * @property OrderModel[] $orders
 */
class SubscriptionModel
{
    public string $id = '';

    public UserModel $user;

    public LicenseModel $license;

    /** @var OrderModel[] */
    private array $orders = [];

    public bool $autoRenew = true;

    public ?UserCardModel $card = null;

    public function addOrder(OrderModel $order): void
    {
        $this->orders[] = $order;
    }

    public function __isset(string $name): bool
    {
        return $name === 'orders';
    }

    /**
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        if ($name !== 'orders') {
            throw new RuntimeException('Invalid property');
        }

        assert(is_array($value));

        array_walk(
            $value,
            [$this, 'addOrder']
        );
    }

    /**
     * @return mixed
     */
    public function __get(string $name)
    {
        if ($name !== 'orders') {
            throw new RuntimeException('Invalid property');
        }

        return $this->orders;
    }
}
