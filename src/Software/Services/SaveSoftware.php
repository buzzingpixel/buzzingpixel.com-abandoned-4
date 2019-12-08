<?php

declare(strict_types=1);

namespace App\Software\Services;

use App\Payload\Payload;
use App\Persistence\SaveExistingRecord;
use App\Persistence\SaveNewRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use App\Software\Models\SoftwareModel;
use App\Software\Models\SoftwareVersionModel;
use App\Software\Transformers\TransformSoftwareModelToRecord;
use App\Software\Transformers\TransformSoftwareVersionModelToRecord;
use Exception;
use PDO;
use Throwable;
use function array_walk;

class SaveSoftware
{
    /** @var PDO */
    private $pdo;
    /** @var UuidFactoryWithOrderedTimeCodec */
    private $uuidFactory;
    /** @var SaveNewRecord */
    private $saveNewRecord;
    /** @var SaveExistingRecord */
    private $saveExistingRecord;
    /** @var TransformSoftwareModelToRecord */
    private $transformSoftwareModelToRecord;
    /** @var TransformSoftwareVersionModelToRecord */
    private $transformSoftwareVersionModelToRecord;

    public function __construct(
        PDO $pdo,
        UuidFactoryWithOrderedTimeCodec $uuidFactory,
        SaveNewRecord $saveNewRecord,
        SaveExistingRecord $saveExistingRecord,
        TransformSoftwareModelToRecord $transformSoftwareModelToRecord,
        TransformSoftwareVersionModelToRecord $transformSoftwareVersionModelToRecord
    ) {
        $this->pdo                                   = $pdo;
        $this->uuidFactory                           = $uuidFactory;
        $this->saveNewRecord                         = $saveNewRecord;
        $this->saveExistingRecord                    = $saveExistingRecord;
        $this->transformSoftwareModelToRecord        = $transformSoftwareModelToRecord;
        $this->transformSoftwareVersionModelToRecord = $transformSoftwareVersionModelToRecord;
    }

    public function __invoke(SoftwareModel $model) : Payload
    {
        try {
            $this->pdo->beginTransaction();

            $isNew = false;

            if ($model->getId() === '') {
                $isNew = true;

                $model->setId($this->uuidFactory->uuid1()->toString());
            }

            $versions = $model->getVersions();

            array_walk($versions, [$this, 'saveVersion']);

            if ($isNew) {
                $record = ($this->transformSoftwareModelToRecord)($model);

                $payload = ($this->saveNewRecord)($record);

                if ($payload->getStatus() === Payload::STATUS_CREATED) {
                    $this->pdo->commit();

                    return $payload;
                }

                throw new Exception('Unknown error saving version');
            }

            $record = ($this->transformSoftwareModelToRecord)($model);

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
        if ($model->getId() === '') {
            $model->setId(
                $this->uuidFactory->uuid1()->toString()
            );

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
