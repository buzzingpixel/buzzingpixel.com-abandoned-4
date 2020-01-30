<?php

declare(strict_types=1);

namespace App\Software\Models;

use App\Payload\Model;
use DateTimeImmutable;
use DateTimeZone;
use Psr\Http\Message\UploadedFileInterface;

class SoftwareVersionModel extends Model
{
    /**
     * @inheritDoc
     */
    public function __construct(array $vars = [])
    {
        parent::__construct($vars);

        /** @psalm-suppress UninitializedProperty */
        if ($this->releasedOn instanceof DateTimeImmutable) {
            return;
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->releasedOn = new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC')
        );
    }

    /** @var string */
    private $id = '';

    public function setId(string $id) : SoftwareVersionModel
    {
        $this->id = $id;

        return $this;
    }

    public function getId() : string
    {
        return $this->id;
    }

    /** @var SoftwareModel|null */
    private $software;

    public function setSoftware(SoftwareModel $software) : SoftwareVersionModel
    {
        $this->software = $software;

        return $this;
    }

    public function getSoftware() : ?SoftwareModel
    {
        return $this->software;
    }

    /** @var string */
    private $majorVersion = '';

    public function setMajorVersion(string $majorVersion) : SoftwareVersionModel
    {
        $this->majorVersion = $majorVersion;

        return $this;
    }

    public function getMajorVersion() : string
    {
        return $this->majorVersion;
    }

    /** @var string */
    private $version = '';

    public function setVersion(string $version) : SoftwareVersionModel
    {
        $this->version = $version;

        return $this;
    }

    public function getVersion() : string
    {
        return $this->version;
    }

    /** @var string */
    private $downloadFile = '';

    public function setDownloadFile(string $downloadFile) : SoftwareVersionModel
    {
        $this->downloadFile = $downloadFile;

        return $this;
    }

    public function getDownloadFile() : string
    {
        return $this->downloadFile;
    }

    /** @var UploadedFileInterface|null */
    private $newDownloadFile;

    public function setNewDownloadFile(?UploadedFileInterface $newDownloadFile) : void
    {
        $this->newDownloadFile = $newDownloadFile;
    }

    public function getNewDownloadFile() : ?UploadedFileInterface
    {
        return $this->newDownloadFile;
    }

    /** @var float */
    private $upgradePrice = 0.0;

    public function setUpgradePrice(float $upgradePrice) : SoftwareVersionModel
    {
        $this->upgradePrice = $upgradePrice;

        return $this;
    }

    public function getUpgradePrice() : float
    {
        return $this->upgradePrice;
    }

    /** @var DateTimeImmutable */
    private $releasedOn;

    public function setReleasedOn(DateTimeImmutable $releasedOn) : void
    {
        $this->releasedOn = $releasedOn;
    }

    public function getReleasedOn() : DateTimeImmutable
    {
        return $this->releasedOn;
    }
}
