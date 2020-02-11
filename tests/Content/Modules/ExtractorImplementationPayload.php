<?php

declare(strict_types=1);

namespace Tests\Content\Modules;

use App\Payload\SpecificPayload;

class ExtractorImplementationPayload extends SpecificPayload
{
    /** @var mixed[] */
    private array $yaml = [];

    /**
     * @param mixed[] $yaml
     */
    protected function setYaml(array $yaml) : void
    {
        $this->yaml = $yaml;
    }

    /**
     * @return mixed[]
     */
    public function getYaml() : array
    {
        return $this->yaml;
    }
}
