<?php

declare(strict_types=1);

namespace App\Licenses;

use App\Licenses\Models\LicenseModel;
use App\Licenses\Services\FetchUsersLicenses;
use App\Licenses\Services\SaveLicenseMaster;
use App\Payload\Payload;
use App\Users\Models\UserModel;
use Psr\Container\ContainerInterface;
use function assert;

class LicenseApi
{
    private ContainerInterface $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function saveLicense(LicenseModel $licenseModel) : Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(SaveLicenseMaster::class);

        assert($service instanceof SaveLicenseMaster);

        return $service($licenseModel);
    }

    /**
     * @return LicenseModel[]
     */
    public function fetchUserLicenses(UserModel $userModel) : array
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchUsersLicenses::class);

        assert($service instanceof FetchUsersLicenses);

        return $service($userModel);
    }
}
