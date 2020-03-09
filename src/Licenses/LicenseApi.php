<?php

declare(strict_types=1);

namespace App\Licenses;

use App\Licenses\Models\LicenseModel;
use App\Licenses\Services\FetchUsersLicenses;
use App\Licenses\Services\OrganizeLicensesByItemKey;
use App\Licenses\Services\SaveLicenseMaster;
use App\Payload\Payload;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use Psr\Container\ContainerInterface;
use Safe\Exceptions\ArrayException;
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

    /**
     * @return LicenseModel[]
     */
    public function fetchCurrentUserLicenses() : array
    {
        /** @psalm-suppress MixedAssignment */
        $userApi = $this->di->get(UserApi::class);

        assert($userApi instanceof UserApi);

        /** @psalm-suppress PossiblyNullArgument */
        return $this->fetchUserLicenses(
            $userApi->fetchLoggedInUser()
        );
    }

    /**
     * @param LicenseModel[] $licenses
     *
     * @return array<string, LicenseModel[]>
     *
     * @throws ArrayException
     *
     * @noinspection PhpDocSignatureInspection
     */
    public function organizeLicensesByItemKey(array $licenses) : array
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(OrganizeLicensesByItemKey::class);

        assert($service instanceof OrganizeLicensesByItemKey);

        return $service($licenses);
    }
}
