<?php

declare(strict_types=1);

namespace App\Software\Services;

use App\Payload\Payload;
use App\Persistence\SaveExistingRecord;
use App\Persistence\SaveNewRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use App\SecureStorage\Services\SaveFileToSecureStorage;
use App\Software\Models\SoftwareModel;
use App\Software\Models\SoftwareVersionModel;
use App\Software\Transformers\TransformSoftwareModelToRecord;
use App\Software\Transformers\TransformSoftwareVersionModelToRecord;
use Exception;
use PDO;
use Throwable;
use function array_walk;
use function assert;

class SaveSoftwareMaster
{
    private PDO $pdo;
    private UuidFactoryWithOrderedTimeCodec $uuidFactory;
    private SaveNewRecord $saveNewRecord;
    private SaveExistingRecord $saveExistingRecord;
    private TransformSoftwareModelToRecord $transformSoftwareModelToRecord;
    private TransformSoftwareVersionModelToRecord $transformSoftwareVersionModelToRecord;
    private SaveFileToSecureStorage $saveFileToSecureStorage;

    public function __construct(
        PDO $pdo,
        UuidFactoryWithOrderedTimeCodec $uuidFactory,
        SaveNewRecord $saveNewRecord,
        SaveExistingRecord $saveExistingRecord,
        TransformSoftwareModelToRecord $transformSoftwareModelToRecord,
        TransformSoftwareVersionModelToRecord $transformSoftwareVersionModelToRecord,
        SaveFileToSecureStorage $saveFileToSecureStorage
    ) {
        $this->pdo                                   = $pdo;
        $this->uuidFactory                           = $uuidFactory;
        $this->saveNewRecord                         = $saveNewRecord;
        $this->saveExistingRecord                    = $saveExistingRecord;
        $this->transformSoftwareModelToRecord        = $transformSoftwareModelToRecord;
        $this->transformSoftwareVersionModelToRecord = $transformSoftwareVersionModelToRecord;
        $this->saveFileToSecureStorage               = $saveFileToSecureStorage;
    }

    public function __invoke(SoftwareModel $model) : Payload
    {
        try {
            $this->pdo->beginTransaction();

            $isNew = false;

            if ($model->id === '') {
                $isNew = true;

                $model->id = $this->uuidFactory->uuid1()->toString();
            }

            $versions = $model->versions;

            array_walk($versions, [$this, 'saveVersion']);

            $record = ($this->transformSoftwareModelToRecord)($model);

            if ($isNew) {
                $payload = ($this->saveNewRecord)($record);

                if ($payload->getStatus() === Payload::STATUS_CREATED) {
                    $this->pdo->commit();

                    return $payload;
                }

                throw new Exception('Unknown error saving version');
            }

            $payload = ($this->saveExistingRecord)($record);

            if ($payload->getStatus() === Payload::STATUS_UPDATED) {
                $this->pdo->commit();

                return $payload;
            }

            throw new Exception('Unknown error saving version');
        } catch (Throwable $e) {
            $this->pdo->rollBack();

            return new Payload(Payload::STATUS_ERROR);
        }
    }

    /**
     * @throws Throwable
     */
    protected function saveVersion(SoftwareVersionModel $model) : void
    {
        $newDownloadFile = $model->newDownloadFile;

        $software = $model->software;
        assert($software instanceof SoftwareModel);

        if ($newDownloadFile !== null) {
            /** @psalm-suppress PossiblyNullReference */
            $slug = $software->slug;

            $saveFilePayload = ($this->saveFileToSecureStorage)(
                $newDownloadFile,
                $slug
            );

            if ($saveFilePayload->getStatus() === Payload::STATUS_SUCCESSFUL) {
                /** @psalm-suppress PossiblyNullOperand */
                $fileName = $newDownloadFile->getClientFilename();

                /** @psalm-suppress PossiblyNullOperand */
                $model->downloadFile = $slug . '/' . $fileName;
            }
        }

        if ($model->id === '') {
            $model->id = $this->uuidFactory->uuid1()->toString();

            $record = ($this->transformSoftwareVersionModelToRecord)($model);

            $payload = ($this->saveNewRecord)($record);

            if ($payload->getStatus() === Payload::STATUS_CREATED) {
                return;
            }

            throw new Exception('Unknown error saving version');
        }

        $record = ($this->transformSoftwareVersionModelToRecord)($model);

        $payload = ($this->saveExistingRecord)($record);

        if ($payload->getStatus() === Payload::STATUS_UPDATED) {
            return;
        }

        throw new Exception('Unknown error saving version');
    }
}
