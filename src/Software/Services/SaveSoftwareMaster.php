<?php

declare(strict_types=1);

namespace App\Software\Services;

use App\Payload\Payload;
use App\Persistence\DatabaseTransactionManager;
use App\Software\Models\SoftwareModel;
use Throwable;
use function array_walk;

class SaveSoftwareMaster
{
    private DatabaseTransactionManager $transactionManager;
    private SaveNewSoftware $saveNewSoftware;
    private SaveExistingSoftware $saveExistingSoftware;
    private SaveSoftwareVersionMaster $saveSoftwareVersionMaster;

    public function __construct(
        DatabaseTransactionManager $transactionManager,
        SaveNewSoftware $saveNewSoftware,
        SaveExistingSoftware $saveExistingSoftware,
        SaveSoftwareVersionMaster $saveSoftwareVersionMaster
    ) {
        $this->transactionManager        = $transactionManager;
        $this->saveNewSoftware           = $saveNewSoftware;
        $this->saveExistingSoftware      = $saveExistingSoftware;
        $this->saveSoftwareVersionMaster = $saveSoftwareVersionMaster;
    }

    public function __invoke(SoftwareModel $model) : Payload
    {
        try {
            $this->transactionManager->beginTransaction();

            if ($model->id === '') {
                ($this->saveNewSoftware)($model);

                $payloadStatus = Payload::STATUS_CREATED;
            } else {
                ($this->saveExistingSoftware)($model);

                $payloadStatus = Payload::STATUS_UPDATED;
            }

            $versions = $model->versions;

            array_walk(
                $versions,
                $this->saveSoftwareVersionMaster
            );

            $this->transactionManager->commit();

            return new Payload($payloadStatus);
        } catch (Throwable $e) {
            $this->transactionManager->rollBack();

            return new Payload(Payload::STATUS_ERROR);
        }
    }
}
