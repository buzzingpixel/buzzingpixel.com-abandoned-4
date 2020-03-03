<?php

declare(strict_types=1);

namespace Tests\Software\Services;

use App\Payload\Payload;
use App\Persistence\SaveExistingRecord;
use App\Persistence\Software\SoftwareVersionRecord;
use App\Software\Models\SoftwareVersionModel;
use App\Software\Services\SaveExistingSoftwareVersion;
use App\Software\Transformers\TransformSoftwareVersionModelToRecord;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;
use Throwable;
use function assert;

class SaveExistingSoftwareVersionTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testSaveExistingSoftware() : void
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
                    SoftwareVersionRecord $record
                ) use ($saveCallHolder) : Payload {
                    $saveCallHolder->record = $record;

                    return new Payload(Payload::STATUS_UPDATED);
                }
            );

        $saveExistingSoftware = new SaveExistingSoftwareVersion(
            $saveExistingRecord,
            new TransformSoftwareVersionModelToRecord(),
        );

        $softwareModel = new SoftwareVersionModel();

        $softwareModel->version = 'U.S.S. Enterprise';

        $saveExistingSoftware($softwareModel);

        assert($saveCallHolder->record instanceof SoftwareVersionRecord);

        self::assertSame(
            $softwareModel->version,
            $saveCallHolder->record->version,
        );
    }

    /**
     * @throws Exception
     */
    public function testSaveExistingSoftwareInvalidPayloadReturnStatus() : void
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
                    SoftwareVersionRecord $record
                ) use ($saveCallHolder) : Payload {
                    $saveCallHolder->record = $record;

                    return new Payload(Payload::STATUS_ERROR);
                }
            );

        $saveExistingSoftware = new SaveExistingSoftwareVersion(
            $saveExistingRecord,
            new TransformSoftwareVersionModelToRecord(),
        );

        $softwareModel = new SoftwareVersionModel();

        $softwareModel->version = 'U.S.S. Enterprise';

        $exception = null;

        try {
            $saveExistingSoftware($softwareModel);
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof Exception);

        self::assertSame(
            'Unknown error saving software',
            $exception->getMessage(),
        );

        assert($saveCallHolder->record instanceof SoftwareVersionRecord);

        self::assertSame(
            $softwareModel->version,
            $saveCallHolder->record->version,
        );
    }
}
