<?php

declare(strict_types=1);

namespace Tests\Software\Services;

use App\Payload\Payload;
use App\Persistence\DatabaseTransactionManager;
use App\Software\Models\SoftwareModel;
use App\Software\Models\SoftwareVersionModel;
use App\Software\Services\SaveExistingSoftware;
use App\Software\Services\SaveNewSoftware;
use App\Software\Services\SaveSoftwareMaster;
use App\Software\Services\SaveSoftwareVersionMaster;
use Exception;
use PHPUnit\Framework\TestCase;

class SaveSoftwareMasterTest extends TestCase
{
    public function testSaveNewSoftware() : void
    {
        $version1 = new SoftwareVersionModel();
        $version2 = new SoftwareVersionModel();

        $software = new SoftwareModel();
        $software->addVersion($version1);
        $software->addVersion($version2);

        $transactionManager = $this->createMock(
            DatabaseTransactionManager::class
        );

        $transactionManager->expects(self::at(0))
            ->method('beginTransaction')
            ->willReturn(true);

        $transactionManager->expects(self::at(1))
            ->method('commit')
            ->willReturn(true);

        $saveNewSoftware = $this->createMock(
            SaveNewSoftware::class
        );

        $saveNewSoftware->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($software));

        $saveExistingSoftware = $this->createMock(
            SaveExistingSoftware::class
        );

        $saveExistingSoftware->expects(self::never())
            ->method(self::anything());

        $saveSoftwareVersionMaster = $this->createMock(
            SaveSoftwareVersionMaster::class
        );

        $saveSoftwareVersionMaster->expects(self::at(0))
            ->method('__invoke')
            ->with(self::equalTo($version1));

        $saveSoftwareVersionMaster->expects(self::at(1))
            ->method('__invoke')
            ->with(self::equalTo($version2));

        $saveSoftware = new SaveSoftwareMaster(
            $transactionManager,
            $saveNewSoftware,
            $saveExistingSoftware,
            $saveSoftwareVersionMaster
        );

        $payload = $saveSoftware($software);

        self::assertSame(
            Payload::STATUS_CREATED,
            $payload->getStatus()
        );

        self::assertSame([], $payload->getResult());
    }

    public function testSaveExistingSoftware() : void
    {
        $version1 = new SoftwareVersionModel();
        $version2 = new SoftwareVersionModel();

        $software     = new SoftwareModel();
        $software->id = 'foo-id';
        $software->addVersion($version1);
        $software->addVersion($version2);

        $transactionManager = $this->createMock(
            DatabaseTransactionManager::class
        );

        $transactionManager->expects(self::at(0))
            ->method('beginTransaction')
            ->willReturn(true);

        $transactionManager->expects(self::at(1))
            ->method('commit')
            ->willReturn(true);

        $saveNewSoftware = $this->createMock(
            SaveNewSoftware::class
        );

        $saveNewSoftware->expects(self::never())
            ->method(self::anything());

        $saveExistingSoftware = $this->createMock(
            SaveExistingSoftware::class
        );

        $saveExistingSoftware->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($software));

        $saveSoftwareVersionMaster = $this->createMock(
            SaveSoftwareVersionMaster::class
        );

        $saveSoftwareVersionMaster->expects(self::at(0))
            ->method('__invoke')
            ->with(self::equalTo($version1));

        $saveSoftwareVersionMaster->expects(self::at(1))
            ->method('__invoke')
            ->with(self::equalTo($version2));

        $saveSoftware = new SaveSoftwareMaster(
            $transactionManager,
            $saveNewSoftware,
            $saveExistingSoftware,
            $saveSoftwareVersionMaster
        );

        $payload = $saveSoftware($software);

        self::assertSame(
            Payload::STATUS_UPDATED,
            $payload->getStatus()
        );

        self::assertSame([], $payload->getResult());
    }

    public function testSaveWhenExceptionThrown() : void
    {
        $software = new SoftwareModel();

        $transactionManager = $this->createMock(
            DatabaseTransactionManager::class
        );

        $transactionManager->expects(self::at(0))
            ->method('beginTransaction')
            ->willThrowException(new Exception());

        $transactionManager->expects(self::at(1))
            ->method('rollBack')
            ->willReturn(true);

        $saveNewSoftware = $this->createMock(
            SaveNewSoftware::class
        );

        $saveNewSoftware->expects(self::never())
            ->method(self::anything());

        $saveExistingSoftware = $this->createMock(
            SaveExistingSoftware::class
        );

        $saveExistingSoftware->expects(self::never())
            ->method(self::anything());

        $saveSoftwareVersionMaster = $this->createMock(
            SaveSoftwareVersionMaster::class
        );

        $saveSoftwareVersionMaster->expects(self::never())
            ->method(self::anything());

        $saveSoftware = new SaveSoftwareMaster(
            $transactionManager,
            $saveNewSoftware,
            $saveExistingSoftware,
            $saveSoftwareVersionMaster
        );

        $payload = $saveSoftware($software);

        self::assertSame(
            Payload::STATUS_ERROR,
            $payload->getStatus()
        );

        self::assertSame([], $payload->getResult());
    }
}
