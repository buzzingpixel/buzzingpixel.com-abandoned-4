<?php

declare(strict_types=1);

namespace App\Software\Models;

use App\Payload\Model;
use function array_walk;

class SoftwareModel extends Model
{
    /** @var string */
    private $id = '';

    public function setId(string $id) : SoftwareModel
    {
        $this->id = $id;

        return $this;
    }

    public function getId() : string
    {
        return $this->id;
    }

    /** @var string */
    private $slug = '';

    public function setSlug(string $slug) : SoftwareModel
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug() : string
    {
        return $this->slug;
    }

    /** @var string */
    private $name = '';

    public function setName(string $name) : SoftwareModel
    {
        $this->name = $name;

        return $this;
    }

    public function getName() : string
    {
        return $this->name;
    }

    /** @var bool */
    private $isForSale = true;

    public function setIsForSale(bool $isForSale) : SoftwareModel
    {
        $this->isForSale = $isForSale;

        return $this;
    }

    public function isForSale() : bool
    {
        return $this->isForSale;
    }

    /** @var SoftwareVersionModel[] */
    private $versions = [];

    /**
     * @param SoftwareVersionModel[] $versions
     */
    public function setVersions(array $versions) : SoftwareModel
    {
        array_walk(
            $versions,
            [$this, 'addVersion']
        );

        return $this;
    }

    public function addVersion(SoftwareVersionModel $softwareVersionModel) : SoftwareModel
    {
        $softwareVersionModel->setSoftware($this);

        $this->versions[] = $softwareVersionModel;

        return $this;
    }

    /**
     * @return SoftwareVersionModel[]
     */
    public function getVersions() : array
    {
        return $this->versions;
    }
}
