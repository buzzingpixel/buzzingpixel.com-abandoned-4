<?php

declare(strict_types=1);

namespace App\Users\Models;

use DateTimeImmutable;

class UserCardModel
{
    public string $id = '';

    public UserModel $user;

    public string $newCardNumber = '';

    public string $stripeId = '';

    public string $nickname = '';

    public string $lastFour = '';

    public string $provider = '';

    public bool $isDefault = false;

    public DateTimeImmutable $expiration;
}
