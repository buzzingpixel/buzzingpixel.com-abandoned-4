<?php

declare(strict_types=1);

namespace App\Users\Models;

use Safe\DateTimeImmutable;

class UserCardModel
{
    public string $id = '';

    public UserModel $user;

    public string $newCardNumber = '';

    public string $newCvc = '';

    public string $stripeId = '';

    public string $nickname = '';

    public string $lastFour = '';

    public string $provider = '';

    public string $nameOnCard = '';

    public string $address = '';

    public string $address2 = '';

    public string $city = '';

    public string $state = '';

    public string $postalCode = '';

    public string $country = '';

    public bool $isDefault = false;

    public DateTimeImmutable $expiration;
}
