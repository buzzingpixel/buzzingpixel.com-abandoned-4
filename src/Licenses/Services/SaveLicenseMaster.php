<?php

declare(strict_types=1);

namespace App\Licenses\Services;

use App\Licenses\Models\LicenseModel;
use App\Payload\Payload;
use App\Persistence\DatabaseTransactionManager;
use Throwable;

class SaveLicenseMaster
{
    private DatabaseTransactionManager $transactionManager;
    private SaveNewLicense $saveNewLicense;
    private SaveExistingLicense $saveExistingLicense;

    public function __construct(
        DatabaseTransactionManager $transactionManager,
        SaveNewLicense $saveNewLicense,
        SaveExistingLicense $saveExistingLicense
    ) {
        $this->transactionManager  = $transactionManager;
        $this->saveNewLicense      = $saveNewLicense;
        $this->saveExistingLicense = $saveExistingLicense;
    }

    public function __invoke(LicenseModel $licenseModel): Payload
    {
        try {
            $this->transactionManager->beginTransaction();

            if ($licenseModel->id === '') {
                ($this->saveNewLicense)($licenseModel);

                $this->transactionManager->commit();

                return new Payload(Payload::STATUS_CREATED);
            }

            ($this->saveExistingLicense)($licenseModel);

            $this->transactionManager->commit();

            return new Payload(Payload::STATUS_UPDATED);
        } catch (Throwable $e) {
            $this->transactionManager->rollBack();

            return new Payload(
                Payload::STATUS_ERROR,
                ['message' => 'An unknown error occurred']
            );
        }
    }
}
