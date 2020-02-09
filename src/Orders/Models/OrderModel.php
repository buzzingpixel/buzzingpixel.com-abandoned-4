<?php

declare(strict_types=1);

namespace App\Orders\Models;

use App\Users\Models\UserModel;
use DateTimeImmutable;
use DateTimeZone;

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

    public string $addressContinued = '';

    public string $city = '';

    public string $state = '';

    public string $postalCode = '';

    public DateTimeImmutable $date;
}
