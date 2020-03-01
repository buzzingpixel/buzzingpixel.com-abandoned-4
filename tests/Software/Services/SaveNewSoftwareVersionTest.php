<?php

declare(strict_types=1);

namespace Tests\Software\Services;

use App\Payload\Payload;
use App\Persistence\SaveNewRecord;
use App\Persistence\Software\SoftwareVersionRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use App\Software\Models\SoftwareVersionModel;
use App\Software\Services\SaveNewSoftwareVersion;
use App\Software\Transformers\TransformSoftwareVersionModelToRecord;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;
use Tests\TestConfig;
use Throwable;
use function assert;

class SaveNewSoftwareVersionTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testSaveNewSoftware() : void
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
                    SoftwareVersionRecord $record
                ) use ($saveCallHolder) : Payload {
                    $saveCallHolder->record = $record;

                    return new Payload(Payload::STATUS_CREATED);
                }
            );

        $saveNewSoftware = new SaveNewSoftwareVersion(
            $saveNewRecord,
            new TransformSoftwareVersionModelToRecord(),
            $uuidFactory,
        );

        $softwareModel = new SoftwareVersionModel();

        $softwareModel->majorVersion = 'U.S.S. Enterprise';

        $saveNewSoftware($softwareModel);

        self::assertSame(
            $uuid->toString(),
            $softwareModel->id,
        );

        assert(
            $saveCallHolder->record instanceof SoftwareVersionRecord
        );

        self::assertSame(
            $uuid->toString(),
            $saveCallHolder->record->id,
        );

        self::assertSame(
            $softwareModel->majorVersion,
            $saveCallHolder->record->major_version,
        );
    }

    /**
     * @throws Exception
     */
    public function testSaveNewSoftwareInvalidPayloadReturnStatus() : void
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
                    SoftwareVersionRecord $record
                ) use ($saveCallHolder) : Payload {
                    $saveCallHolder->record = $record;

                    return new Payload(Payload::STATUS_ERROR);
                }
            );

        $saveNewSoftware = new SaveNewSoftwareVersion(
            $saveNewRecord,
            new TransformSoftwareVersionModelToRecord(),
            $uuidFactory,
        );

        $softwareModel = new SoftwareVersionModel();

        $softwareModel->majorVersion = 'U.S.S. Enterprise';

        $exception = null;

        try {
            $saveNewSoftware($softwareModel);
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof Exception);

        self::assertSame(
            'Unknown error saving software version',
            $exception->getMessage(),
        );

        self::assertSame(
            $uuid->toString(),
            $softwareModel->id,
        );

        assert(
            $saveCallHolder->record instanceof SoftwareVersionRecord
        );

        self::assertSame(
            $uuid->toString(),
            $saveCallHolder->record->id,
        );

        self::assertSame(
            $softwareModel->majorVersion,
            $saveCallHolder->record->major_version,
        );
    }
}
