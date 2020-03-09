<?php

declare(strict_types=1);

namespace App\Licenses\Services;

use App\Licenses\Models\LicenseModel;
use Safe\Exceptions\ArrayException;
use function Safe\ksort;
use const SORT_NATURAL;

class OrganizeLicensesByItemKey
{
    /**
     * @param LicenseModel[] $licenses
     *
     * @return array<string, LicenseModel[]>
     *
     * @throws ArrayException
     *
     * @psalm-suppress MixedReturnTypeCoercion
     * @noinspection PhpDocSignatureInspection
     */
    public function __invoke(array $licenses) : array
    {
        $returnItems = [];

        foreach ($licenses as $license) {
            $returnItems[$license->itemKey][] = $license;
        }

        ksort($returnItems, SORT_NATURAL);

        /** @psalm-suppress MixedReturnTypeCoercion */
        return $returnItems;
    }
}
