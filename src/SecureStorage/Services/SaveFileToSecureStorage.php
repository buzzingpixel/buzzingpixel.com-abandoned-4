<?php

declare(strict_types=1);

namespace App\SecureStorage\Services;

use App\Payload\Payload;
use Config\General;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

class SaveFileToSecureStorage
{
    /** @var General */
    private $generalConfig;
    /** @var Filesystem */
    private $filesystem;

    public function __construct(
        General $generalConfig,
        Filesystem $filesystem
    ) {
        $this->generalConfig = $generalConfig;
        $this->filesystem    = $filesystem;
    }

    public function __invoke(
        UploadedFileInterface $uploadedFile,
        string $directory = ''
    ) : Payload {
        try {
            $finalPath = $this->generalConfig->pathToSecureStorageDirectory();

            if ($directory !== '') {
                $finalPath .= '/' . $directory;
            }

            $this->filesystem->mkdir($finalPath);

            /** @psalm-suppress PossiblyNullOperand */
            $finalPath .= '/' . $uploadedFile->getClientFilename();

            $uploadedFile->moveTo($finalPath);

            return new Payload(Payload::STATUS_SUCCESSFUL);
        } catch (Throwable $e) {
            return new Payload(Payload::STATUS_ERROR);
        }
    }
}
