<?php

declare(strict_types=1);

namespace App\Content\Meta;

use App\Payload\SpecificPayload;

class MetaPayload extends SpecificPayload
{
    /** @var string */
    private $metaTitle = '';

    protected function setMetaTitle(string $metaTitle) : void
    {
        $this->metaTitle = $metaTitle;
    }

    public function getMetaTitle() : string
    {
        return $this->metaTitle;
    }
}
