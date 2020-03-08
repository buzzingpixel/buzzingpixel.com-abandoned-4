<?php

declare(strict_types=1);

namespace Tests\Licenses\Services;

use App\Licenses\Models\LicenseModel;
use App\Licenses\Services\SaveNewLicense;
use App\Licenses\Transformers\TransformLicenseModelToRecord;
use App\Payload\Payload;
use App\Persistence\Licenses\LicenseRecord;
use App\Persistence\SaveNewRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use App\Users\Models\UserModel;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;
use Tests\TestConfig;
use Throwable;
use function assert;

class SaveNewLicenseTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testSaveNewLicense() : void
    {
        $uuid = TestConfig::$di->get(UuidFactoryWithOrderedTimeCodec::class)
            ->uuid1();

        $uuidFactory = $this->createMock(
            UuidFactoryWithOrderedTimeCodec::class,
        );

        $uuidFactory->expects(self::once())
            ->method('uuid1')
            ->willReturn($uuid);

        $saveNewRecord = $this->createMock(
            SaveNewRecord::class,
        );

        $saveCallHolder = new stdClass();

        $saveCallHolder->record = null;

        $saveNewRecord->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    LicenseRecord $record
                ) use ($saveCallHolder) : Payload {
                    $saveCallHolder->record = $record;

                    return new Payload(Payload::STATUS_CREATED);
                }
            );

        $saveNewLicense = new SaveNewLicense(
            $saveNewRecord,
            new TransformLicenseModelToRecord(),
            $uuidFactory,
        );

        $licenseModel = new LicenseModel();

        $licenseModel->ownerUser = new UserModel();

        $licenseModel->notes = 'U.S.S. Saratoga';

        $saveNewLicense($licenseModel);

        self::assertSame(
            $uuid->toString(),
            $licenseModel->id,
        );

        assert($saveCallHolder->record instanceof LicenseRecord);

        self::assertSame(
            $uuid->toString(),
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
    public function testSaveNewLicenseInvalidPayloadReturnStatus() : void
    {
        $uuid = TestConfig::$di->get(UuidFactoryWithOrderedTimeCodec::class)
            ->uuid1();

        $uuidFactory = $this->createMock(
            UuidFactoryWithOrderedTimeCodec::class,
        );

        $uuidFactory->expects(self::once())
            ->method('uuid1')
            ->willReturn($uuid);

        $saveNewRecord = $this->createMock(
            SaveNewRecord::class,
        );

        $saveCallHolder = new stdClass();

        $saveCallHolder->record = null;

        $saveNewRecord->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    LicenseRecord $record
                ) use ($saveCallHolder) : Payload {
                    $saveCallHolder->record = $record;

                    return new Payload(Payload::STATUS_ERROR);
                }
            );

        $saveNewLicense = new SaveNewLicense(
            $saveNewRecord,
            new TransformLicenseModelToRecord(),
            $uuidFactory,
        );

        $licenseModel = new LicenseModel();

        $licenseModel->ownerUser = new UserModel();

        $licenseModel->notes = 'U.S.S. Saratoga';

        $exception = null;

        try {
            $saveNewLicense($licenseModel);
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof Exception);

        self::assertSame(
            'Unknown error saving license',
            $exception->getMessage(),
        );

        self::assertSame(
            $uuid->toString(),
            $licenseModel->id,
        );

        assert($saveCallHolder->record instanceof LicenseRecord);

        self::assertSame(
            $uuid->toString(),
            $saveCallHolder->record->id,
        );

        self::assertSame(
            $licenseModel->notes,
            $saveCallHolder->record->notes,
        );
    }
}
