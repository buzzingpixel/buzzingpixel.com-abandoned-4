<?php

declare(strict_types=1);

namespace App\Licenses\Models;

use App\Users\Models\UserModel;
use Safe\DateTimeImmutable;

class LicenseModel
{
    public string $id = '';

    public ?UserModel $ownerUser;

    public string $itemKey = '';

    public string $itemTitle = '';

    public string $majorVersion = '';

    public string $version = '';

    public string $lastAvailableVersion = '';

    public string $notes = '';

    /** @var string[] */
    public array $authorizedDomains = [];

    public bool $isDisabled = false;

    public ?DateTimeImmutable $expires = null;
}
