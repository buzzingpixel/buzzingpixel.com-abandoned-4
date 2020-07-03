<?php

declare(strict_types=1);

namespace App\Licenses;

use App\Licenses\Models\LicenseModel;
use App\Licenses\Services\FetchLicenseById;
use App\Licenses\Services\FetchUserLicenseById;
use App\Licenses\Services\FetchUsersLicenses;
use App\Licenses\Services\LicenseStatus;
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

    public function saveLicense(LicenseModel $licenseModel): Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(SaveLicenseMaster::class);

        assert($service instanceof SaveLicenseMaster);

        return $service($licenseModel);
    }

    /**
     * @return LicenseModel[]
     */
    public function fetchUserLicenses(UserModel $userModel): array
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchUsersLicenses::class);

        assert($service instanceof FetchUsersLicenses);

        return $service($userModel);
    }

    /**
     * @return LicenseModel[]
     */
    public function fetchCurrentUserLicenses(): array
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
    public function organizeLicensesByItemKey(array $licenses): array
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(OrganizeLicensesByItemKey::class);

        assert($service instanceof OrganizeLicensesByItemKey);

        return $service($licenses);
    }

    public function fetchUserLicenseById(
        UserModel $user,
        string $id
    ): ?LicenseModel {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchUserLicenseById::class);

        assert($service instanceof FetchUserLicenseById);

        return $service($user, $id);
    }

    public function fetchCurrentUserLicenseById(string $id): ?LicenseModel
    {
        /** @psalm-suppress MixedAssignment */
        $userApi = $this->di->get(UserApi::class);

        assert($userApi instanceof UserApi);

        /** @psalm-suppress PossiblyNullArgument */
        return $this->fetchUserLicenseById(
            $userApi->fetchLoggedInUser(),
            $id,
        );
    }

    public function fetchLicenseById(
        string $id,
        ?UserModel $ownerUser = null
    ): ?LicenseModel {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchLicenseById::class);

        assert($service instanceof FetchLicenseById);

        return $service($id, $ownerUser);
    }

    public function licenseStatus(): LicenseStatus
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(LicenseStatus::class);

        assert($service instanceof LicenseStatus);

        return $service;
    }
}
