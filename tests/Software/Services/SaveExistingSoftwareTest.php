<?php

declare(strict_types=1);

namespace Tests\Software\Services;

use App\Payload\Payload;
use App\Persistence\SaveExistingRecord;
use App\Persistence\Software\SoftwareRecord;
use App\Software\Models\SoftwareModel;
use App\Software\Services\SaveExistingSoftware;
use App\Software\Transformers\TransformSoftwareModelToRecord;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;
use Throwable;
use function assert;

class SaveExistingSoftwareTest extends TestCase
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
                    SoftwareRecord $record
                ) use ($saveCallHolder) : Payload {
                    $saveCallHolder->record = $record;

                    return new Payload(Payload::STATUS_UPDATED);
                }
            );

        $saveExistingSoftware = new SaveExistingSoftware(
            $saveExistingRecord,
            new TransformSoftwareModelToRecord(),
        );

        $softwareModel = new SoftwareModel();

        $softwareModel->name = 'U.S.S. Enterprise';

        $saveExistingSoftware($softwareModel);

        assert($saveCallHolder->record instanceof SoftwareRecord);

        self::assertSame(
            $softwareModel->name,
            $saveCallHolder->record->name,
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
                    SoftwareRecord $record
                ) use ($saveCallHolder) : Payload {
                    $saveCallHolder->record = $record;

                    return new Payload(Payload::STATUS_ERROR);
                }
            );

        $saveExistingSoftware = new SaveExistingSoftware(
            $saveExistingRecord,
            new TransformSoftwareModelToRecord(),
        );

        $softwareModel = new SoftwareModel();

        $softwareModel->name = 'U.S.S. Enterprise';

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

        assert($saveCallHolder->record instanceof SoftwareRecord);

        self::assertSame(
            $softwareModel->name,
            $saveCallHolder->record->name,
        );
    }
}
