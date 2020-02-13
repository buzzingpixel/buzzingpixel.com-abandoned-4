<?php

declare(strict_types=1);

namespace Tests\Licenses\Services;

use App\Licenses\Models\LicenseModel;
use App\Licenses\Services\SaveExistingLicense;
use App\Licenses\Services\SaveLicenseMaster;
use App\Licenses\Services\SaveNewLicense;
use App\Payload\Payload;
use App\Persistence\DatabaseTransactionManager;
use Exception;
use PHPUnit\Framework\TestCase;

class SaveLicenseMasterTest extends TestCase
{
    public function testSaveNewLicense() : void
    {
        $licenseModel = new LicenseModel();

        $transactionManager = $this->createMock(
            DatabaseTransactionManager::class
        );

        $transactionManager->expects(self::at(0))
            ->method('beginTransaction')
            ->willReturn(true);

        $transactionManager->expects(self::at(1))
            ->method('commit')
            ->willReturn(true);

        $saveNewLicense = $this->createMock(
            SaveNewLicense::class
        );

        $saveNewLicense->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($licenseModel));

        $saveExistingLicense = $this->createMock(
            SaveExistingLicense::class
        );

        $saveExistingLicense->expects(self::never())->method(self::anything());

        $saveLicense = new SaveLicenseMaster(
            $transactionManager,
            $saveNewLicense,
            $saveExistingLicense
        );

        $payload = $saveLicense($licenseModel);

        self::assertSame(
            Payload::STATUS_CREATED,
            $payload->getStatus()
        );

        self::assertSame([], $payload->getResult());
    }

    public function testSaveExistingLicense() : void
    {
        $licenseModel     = new LicenseModel();
        $licenseModel->id = 'foo';

        $transactionManager = $this->createMock(
            DatabaseTransactionManager::class
        );

        $transactionManager->expects(self::at(0))
            ->method('beginTransaction')
            ->willReturn(true);

        $transactionManager->expects(self::at(1))
            ->method('commit')
            ->willReturn(true);

        $saveNewLicense = $this->createMock(
            SaveNewLicense::class
        );

        $saveNewLicense->expects(self::never())->method(self::anything());

        $saveExistingLicense = $this->createMock(
            SaveExistingLicense::class
        );

        $saveExistingLicense->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($licenseModel));

        $saveLicense = new SaveLicenseMaster(
            $transactionManager,
            $saveNewLicense,
            $saveExistingLicense
        );

        $payload = $saveLicense($licenseModel);

        self::assertSame(
            Payload::STATUS_UPDATED,
            $payload->getStatus()
        );

        self::assertSame([], $payload->getResult());
    }

    public function testSaveWhenException() : void
    {
        $licenseModel     = new LicenseModel();
        $licenseModel->id = 'foo';

        $transactionManager = $this->createMock(
            DatabaseTransactionManager::class
        );

        $transactionManager->expects(self::at(0))
            ->method('beginTransaction')
            ->willThrowException(new Exception());

        $transactionManager->expects(self::at(1))
            ->method('rollBack')
            ->willReturn(true);

        $saveNewLicense = $this->createMock(
            SaveNewLicense::class
        );

        $saveNewLicense->expects(self::never())->method(self::anything());

        $saveExistingLicense = $this->createMock(
            SaveExistingLicense::class
        );

        $saveExistingLicense->expects(self::never())->method(self::anything());

        $saveLicense = new SaveLicenseMaster(
            $transactionManager,
            $saveNewLicense,
            $saveExistingLicense
        );

        $payload = $saveLicense($licenseModel);

        self::assertSame(
            Payload::STATUS_ERROR,
            $payload->getStatus()
        );

        self::assertSame(
            ['message' => 'An unknown error occurred'],
            $payload->getResult()
        );
    }
}
