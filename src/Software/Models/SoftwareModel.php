<?php

declare(strict_types=1);

namespace App\Software\Models;

use RuntimeException;

use function assert;
use function is_array;

/**
 * @property SoftwareVersionModel[] $versions
 */
class SoftwareModel
{
    /**
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        if ($name !== 'versions') {
            throw new RuntimeException('Invalid property');
        }

        assert(is_array($value));

        /** @psalm-suppress MixedAssignment */
        foreach ($value as $version) {
            assert($version instanceof SoftwareVersionModel);

            $this->addVersion($version);
        }
    }

    /**
     * @return mixed
     */
    public function __get(string $name)
    {
        if ($name !== 'versions') {
            throw new RuntimeException('Invalid property');
        }

        return $this->versions;
    }

    public function __isset(string $name): bool
    {
        return $name === 'versions';
    }

    public string $id = '';

    public string $slug = '';

    public string $name = '';

    public bool $isForSale = true;

    public float $price = 0.0;

    public float $renewalPrice = 0.0;

    public bool $isSubscription = false;

    /** @var SoftwareVersionModel[] */
    private array $versions = [];

    public function addVersion(SoftwareVersionModel $softwareVersionModel): SoftwareModel
    {
        $softwareVersionModel->software = $this;

        $this->versions[] = $softwareVersionModel;

        return $this;
    }
}
