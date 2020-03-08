<?php

declare(strict_types=1);

namespace Tests\Licenses\Services;

use App\Licenses\Models\LicenseModel;
use App\Licenses\Services\SaveExistingLicense;
use App\Licenses\Transformers\TransformLicenseModelToRecord;
use App\Payload\Payload;
use App\Persistence\Licenses\LicenseRecord;
use App\Persistence\SaveExistingRecord;
use App\Users\Models\UserModel;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;
use Throwable;
use function assert;

class SaveExistingLicenseTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testSaveExistingLicense() : void
    {
        $saveExistingRecord = $this->createMock(
            SaveExistingRecord::class,
        );

        $saveCallHolder = new stdClass();

        $saveCallHolder->record = null;

        $saveExistingRecord->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    LicenseRecord $record
                ) use ($saveCallHolder) : Payload {
                    $saveCallHolder->record = $record;

                    return new Payload(Payload::STATUS_UPDATED);
                }
            );

        $saveExistingLicense = new SaveExistingLicense(
            $saveExistingRecord,
            new TransformLicenseModelToRecord(),
        );

        $licenseModel = new LicenseModel();

        $licenseModel->ownerUser = new UserModel();

        $licenseModel->id = 'fooId';

        $licenseModel->notes = 'U.S.S. Enterprise';

        $saveExistingLicense($licenseModel);

        assert($saveCallHolder->record instanceof LicenseRecord);

        self::assertSame(
            $licenseModel->id,
            $saveCallHolder->record->id,
        );

        self::assertSame(
            $licenseModel->notes,
            $saveCallHolder->record->notes,
        );
    }

    /**
     * @throws Exception
     */
    public function testSaveExistingLicenseInvalidPayloadReturnStatus() : void
    {
        $saveExistingRecord = $this->createMock(
            SaveExistingRecord::class,
        );

        $saveCallHolder = new stdClass();

        $saveCallHolder->record = null;

        $saveExistingRecord->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    LicenseRecord $record
                ) use ($saveCallHolder) : Payload {
                    $saveCallHolder->record = $record;

                    return new Payload(Payload::STATUS_ERROR);
                }
            );

        $saveExistingLicense = new SaveExistingLicense(
            $saveExistingRecord,
            new TransformLicenseModelToRecord(),
        );

        $licenseModel = new LicenseModel();

        $licenseModel->ownerUser = new UserModel();

        $licenseModel->id = 'fooId';

        $licenseModel->notes = 'U.S.S. Enterprise';

        $exception = null;

        try {
            $saveExistingLicense($licenseModel);
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof Exception);

        self::assertSame(
            'Unknown error saving license',
            $exception->getMessage(),
        );

        assert($saveCallHolder->record instanceof LicenseRecord);

        self::assertSame(
            $licenseModel->id,
            $saveCallHolder->record->id,
        );

        self::assertSame(
            $licenseModel->notes,
            $saveCallHolder->record->notes,
        );
    }
}
