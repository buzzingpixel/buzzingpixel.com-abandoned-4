<?php

declare(strict_types=1);

namespace App\Content\Documentation;

use App\Content\Software\SoftwareInfoPayload;
use App\Payload\SpecificPayload;
use InvalidArgumentException;
use function array_walk;

class DocumentationVersionsPayload extends SpecificPayload
{
    /**
     * @inheritDoc
     */
    public function __construct(array $vars = [])
    {
        parent::__construct($vars);

        /** @psalm-suppress RedundantCondition */
        if ($this->softwareInfo !== null) {
            return;
        }

        throw new InvalidArgumentException('SoftwareInfo is required');
    }

    /** @psalm-suppress PropertyNotSetInConstructor */
    private SoftwareInfoPayload $softwareInfo;

    protected function setSoftwareInfo(SoftwareInfoPayload $softwareInfoPayload) : void
    {
        $this->softwareInfo = $softwareInfoPayload;
    }

    public function getSoftwareInfo() : SoftwareInfoPayload
    {
        return $this->softwareInfo;
    }

    /** @var DocumentationVersionPayload[] */
    private array $versions = [];

    /**
     * @param DocumentationVersionPayload[] $versions
     */
    protected function setVersions(array $versions) : void
    {
        array_walk($versions, [$this, 'addVersion']);
    }

    protected function addVersion(DocumentationVersionPayload $version) : void
    {
        $this->versions[$version->getSlug()] = $version;
    }

    /**
     * @return DocumentationVersionPayload[]
     */
    public function getVersions() : array
    {
        return $this->versions;
    }

    public function getVersionBySlug(string $slug) : ?DocumentationVersionPayload
    {
        return $this->versions[$slug] ?? null;
    }
}
