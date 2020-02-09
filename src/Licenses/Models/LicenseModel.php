<?php

declare(strict_types=1);

namespace App\Licenses\Models;

class LicenseModel
{
    public string $id = '';

    public string $itemKey = '';

    public string $itemTitle = '';

    public string $majorVersion = '';

    public string $version = '';

    public string $lastAvailableVersion = '';

    public string $notes = '';

    /** @var string[] */
    public array $authorizedDomains = [];

    public bool $isDisabled;
}
