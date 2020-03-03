<?php

declare(strict_types=1);

namespace Tests\Software\Services;

use App\Payload\Payload;
use App\SecureStorage\Services\SaveFileToSecureStorage;
use App\Software\Models\SoftwareModel;
use App\Software\Models\SoftwareVersionModel;
use App\Software\Services\SaveExistingSoftwareVersion;
use App\Software\Services\SaveNewSoftwareVersion;
use App\Software\Services\SaveSoftwareVersionMaster;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use Throwable;

class SaveSoftwareVersionMasterTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testSaveNewVersionWithNoDownloadFile() : void
    {
        $model = new SoftwareVersionModel();

        $saveNewSoftwareVersion = $this->createMock(
            SaveNewSoftwareVersion::class
        );

        $saveNewSoftwareVersion->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($model));

        $saveExistingSoftwareVersion = $this->createMock(
            SaveExistingSoftwareVersion::class
        );

        $saveExistingSoftwareVersion->expects(self::never())
            ->method(self::anything());

        $saveFileToSecureStorage = $this->createMock(
            SaveFileToSecureStorage::class
        );

        $saveFileToSecureStorage->expects(self::never())
            ->method(self::anything());

        $saveSoftwareVersionMaster = new SaveSoftwareVersionMaster(
            $saveNewSoftwareVersion,
            $saveExistingSoftwareVersion,
            $saveFileToSecureStorage
        );

        $saveSoftwareVersionMaster($model);
    }

    /**
     * @throws Throwable
     */
    public function testSaveExistingVersionDownloadFileFails() : void
    {
        $downloadFile = $this->createMock(
            UploadedFileInterface::class
        );

        $software       = new SoftwareModel();
        $software->slug = 'foo-software-slug';

        $model                  = new SoftwareVersionModel();
        $model->id              = 'foo';
        $model->newDownloadFile = $downloadFile;
        $model->software        = $software;

        $saveNewSoftwareVersion = $this->createMock(
            SaveNewSoftwareVersion::class
        );

        $saveNewSoftwareVersion->expects(self::never())
            ->method(self::anything());

        $saveExistingSoftwareVersion = $this->createMock(
            SaveExistingSoftwareVersion::class
        );

        $saveExistingSoftwareVersion->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($model));

        $saveFileToSecureStorage = $this->createMock(
            SaveFileToSecureStorage::class
        );

        $saveFileToSecureStorage->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo($downloadFile),
                self::equalTo('foo-software-slug')
            )
            ->willReturn(new Payload(Payload::STATUS_ERROR));

        $saveSoftwareVersionMaster = new SaveSoftwareVersionMaster(
            $saveNewSoftwareVersion,
            $saveExistingSoftwareVersion,
            $saveFileToSecureStorage
        );

        $saveSoftwareVersionMaster($model);
    }

    /**
     * @throws Throwable
     */
    public function testSaveExistingVersionDownloadFileSucceeds() : void
    {
        $downloadFile = $this->createMock(
            UploadedFileInterface::class
        );

        $downloadFile->method('getClientFilename')
            ->willReturn('baz-file-name');

        $software       = new SoftwareModel();
        $software->slug = 'foo-software-slug';

        $model                  = new SoftwareVersionModel();
        $model->id              = 'foo';
        $model->newDownloadFile = $downloadFile;
        $model->software        = $software;

        $saveNewSoftwareVersion = $this->createMock(
            SaveNewSoftwareVersion::class
        );

        $saveNewSoftwareVersion->expects(self::never())
            ->method(self::anything());

        $saveExistingSoftwareVersion = $this->createMock(
            SaveExistingSoftwareVersion::class
        );

        $saveExistingSoftwareVersion->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($model));

        $saveFileToSecureStorage = $this->createMock(
            SaveFileToSecureStorage::class
        );

        $saveFileToSecureStorage->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo($downloadFile),
                self::equalTo('foo-software-slug')
            )
            ->willReturn(new Payload(Payload::STATUS_SUCCESSFUL));

        $saveSoftwareVersionMaster = new SaveSoftwareVersionMaster(
            $saveNewSoftwareVersion,
            $saveExistingSoftwareVersion,
            $saveFileToSecureStorage
        );

        $saveSoftwareVersionMaster($model);

        self::assertSame(
            'foo-software-slug/baz-file-name',
            $model->downloadFile,
        );
    }
}
