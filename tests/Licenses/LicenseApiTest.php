<?php

declare(strict_types=1);

namespace Tests\Licenses;

use App\Licenses\LicenseApi;
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
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Throwable;

class LicenseApiTest extends TestCase
{
    public function testSaveLicense(): void
    {
        $payload = new Payload(Payload::STATUS_SUCCESSFUL);

        $license = new LicenseModel();

        $service = $this->createMock(
            SaveLicenseMaster::class
        );

        $service->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($license))
            ->willReturn($payload);

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(self::equalTo(SaveLicenseMaster::class))
            ->willReturn($service);

        $api = new LicenseApi($di);

        self::assertSame(
            $payload,
            $api->saveLicense($license)
        );
    }

    public function testFetchUserLicenses(): void
    {
        $license = new LicenseModel();

        $user = new UserModel();

        $service = $this->createMock(
            FetchUsersLicenses::class
        );

        $service->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($user))
            ->willReturn([$license]);

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(self::equalTo(FetchUsersLicenses::class))
            ->willReturn($service);

        $api = new LicenseApi($di);

        self::assertSame(
            [$license],
            $api->fetchUserLicenses($user)
        );
    }

    public function testFetchCurrentUserLicenses(): void
    {
        $license = new LicenseModel();

        $user = new UserModel();

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        $service = $this->createMock(
            FetchUsersLicenses::class
        );

        $service->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($user))
            ->willReturn([$license]);

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::at(0))
            ->method('get')
            ->with(self::equalTo(UserApi::class))
            ->willReturn($userApi);

        $di->expects(self::at(1))
            ->method('get')
            ->with(self::equalTo(FetchUsersLicenses::class))
            ->willReturn($service);

        $api = new LicenseApi($di);

        self::assertSame(
            [$license],
            $api->fetchCurrentUserLicenses()
        );
    }

    /**
     * @throws Throwable
     */
    public function testOrganizeLicensesByItemKey(): void
    {
        $license = new LicenseModel();

        $service = $this->createMock(
            OrganizeLicensesByItemKey::class
        );

        $service->expects(self::once())
            ->method('__invoke')
            ->with([$license])
            ->willReturn([$license]);

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(
                self::equalTo(OrganizeLicensesByItemKey::class)
            )
            ->willReturn($service);

        $api = new LicenseApi($di);

        self::assertSame(
            [$license],
            $api->organizeLicensesByItemKey([$license])
        );
    }

    public function testFetchCurrentUserLicenseById(): void
    {
        $license = new LicenseModel();

        $user = new UserModel();

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        $service = $this->createMock(
            FetchUserLicenseById::class
        );

        $service->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo($user),
                self::equalTo('foo-id'),
            )
            ->willReturn($license);

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::at(0))
            ->method('get')
            ->with(self::equalTo(UserApi::class))
            ->willReturn($userApi);

        $di->expects(self::at(1))
            ->method('get')
            ->with(
                self::equalTo(FetchUserLicenseById::class)
            )
            ->willReturn($service);

        $api = new LicenseApi($di);

        self::assertSame(
            $license,
            $api->fetchCurrentUserLicenseById('foo-id'),
        );
    }

    public function testFetchLicenseById(): void
    {
        $license = new LicenseModel();

        $user = new UserModel();

        $fetchLicenseById = $this->createMock(
            FetchLicenseById::class
        );

        $fetchLicenseById->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo('foo-id'),
                self::equalTo($user)
            )
            ->willReturn($license);

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(self::equalTo(FetchLicenseById::class))
            ->willReturn($fetchLicenseById);

        $api = new LicenseApi($di);

        self::assertSame(
            $license,
            $api->fetchLicenseById('foo-id', $user)
        );
    }

    public function testLicenseStatus(): void
    {
        $service = $this->createMock(LicenseStatus::class);

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(self::equalTo(LicenseStatus::class))
            ->willReturn($service);

        $api = new LicenseApi($di);

        self::assertSame(
            $service,
            $api->licenseStatus(),
        );
    }
}
