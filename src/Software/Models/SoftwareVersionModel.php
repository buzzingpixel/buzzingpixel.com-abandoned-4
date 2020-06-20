<?php

declare(strict_types=1);

namespace App\Software\Models;

use DateTimeZone;
use Psr\Http\Message\UploadedFileInterface;
use Safe\DateTimeImmutable;

class SoftwareVersionModel
{
    public function __construct()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->releasedOn = new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC')
        );
    }

    public string $id = '';

    public ?SoftwareModel $software = null;

    public string $majorVersion = '';

    public string $version = '';

    public string $downloadFile = '';

    public ?UploadedFileInterface $newDownloadFile = null;

    public float $upgradePrice = 0.0;

    public DateTimeImmutable $releasedOn;
}
