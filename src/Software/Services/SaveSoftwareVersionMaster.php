<?php

declare(strict_types=1);

namespace App\Software\Services;

use App\Payload\Payload;
use App\SecureStorage\Services\SaveFileToSecureStorage;
use App\Software\Models\SoftwareModel;
use App\Software\Models\SoftwareVersionModel;
use Exception;
use Psr\Http\Message\UploadedFileInterface;
use function assert;

/**
 * This should only be invoked from SaveSoftwareMaster where the PDO transaction
 * is begun and exception handling is in place
 */
class SaveSoftwareVersionMaster
{
    private SaveNewSoftwareVersion $saveNewSoftwareVersion;
    private SaveExistingSoftwareVersion $saveExistingSoftwareVersion;
    private SaveFileToSecureStorage $saveFileToSecureStorage;

    public function __construct(
        SaveNewSoftwareVersion $saveNewSoftwareVersion,
        SaveExistingSoftwareVersion $saveExistingSoftwareVersion,
        SaveFileToSecureStorage $saveFileToSecureStorage
    ) {
        $this->saveNewSoftwareVersion      = $saveNewSoftwareVersion;
        $this->saveExistingSoftwareVersion = $saveExistingSoftwareVersion;
        $this->saveFileToSecureStorage     = $saveFileToSecureStorage;
    }

    /**
     * @throws Exception
     */
    public function __invoke(SoftwareVersionModel $model) : void
    {
        $this->processNewDownloadFile(
            $model->newDownloadFile,
            $model
        );

        if ($model->id === '') {
            ($this->saveNewSoftwareVersion)($model);
        } else {
            ($this->saveExistingSoftwareVersion)($model);
        }
    }

    private function processNewDownloadFile(
        ?UploadedFileInterface $downloadFile,
        SoftwareVersionModel $model
    ) : void {
        if ($downloadFile === null) {
            return;
        }

        $software = $model->software;

        assert($software instanceof SoftwareModel);

        /** @psalm-suppress PossiblyNullReference */
        $slug = $software->slug;

        $saveFilePayload = ($this->saveFileToSecureStorage)(
            $downloadFile,
            $slug
        );

        if ($saveFilePayload->getStatus() !== Payload::STATUS_SUCCESSFUL) {
            return;
        }

        /** @psalm-suppress PossiblyNullOperand */
        $fileName = $downloadFile->getClientFilename();

        /** @psalm-suppress PossiblyNullOperand */
        $model->downloadFile = $slug . '/' . $fileName;
    }
}
