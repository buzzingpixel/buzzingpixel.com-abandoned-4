<?php

declare(strict_types=1);

namespace Tests\Licenses;

use App\Licenses\LicenseApi;
use App\Licenses\Models\LicenseModel;
use App\Licenses\Services\FetchUsersLicenses;
use App\Licenses\Services\OrganizeLicensesByItemKey;
use App\Licenses\Services\SaveLicenseMaster;
use App\Payload\Payload;
use App\Users\Models\UserModel;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Throwable;

class LicenseApiTest extends TestCase
{
    public function testSaveLicense() : void
    {
        $payload = new Payload(Payload::STATUS_SUCCESSFUL);

        $license = new LicenseModel();

        $service = $this->createMock(SaveLicenseMaster::class);

        $service->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($license))
            ->willReturn($payload);

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->willReturn($service);

        $api = new LicenseApi($di);

        self::assertSame(
            $payload,
            $api->saveLicense($license)
        );
    }

    public function testFetchUserLicenses() : void
    {
        $license = new LicenseModel();

        $user = new UserModel();

        $service = $this->createMock(FetchUsersLicenses::class);

        $service->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($user))
            ->willReturn([$license]);

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->willReturn($service);

        $api = new LicenseApi($di);

        self::assertSame(
            [$license],
            $api->fetchUserLicenses($user)
        );
    }

    /**
     * @throws Throwable
     */
    public function testOrganizeLicensesByItemKey() : void
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
            ->willReturn($service);

        $api = new LicenseApi($di);

        self::assertSame(
            [$license],
            $api->organizeLicensesByItemKey([$license])
        );
    }
}
