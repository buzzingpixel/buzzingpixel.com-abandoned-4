<?php

declare(strict_types=1);

namespace App\Content\Meta;

use App\Payload\SpecificPayload;

class MetaPayload extends SpecificPayload
{
    /** @var bool */
    private $noIndex = false;

    protected function setNoIndex(bool $noIndex) : void
    {
        $this->noIndex = $noIndex;
    }

    public function getNoIndex() : bool
    {
        return $this->noIndex;
    }

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

    /** @var string */
    private $metaDescription = '';

    protected function setMetaDescription(string $metaDescription) : void
    {
        $this->metaDescription = $metaDescription;
    }

    public function getMetaDescription() : string
    {
        return $this->metaDescription;
    }

    /** @var string */
    private $ogType = 'website';

    protected function setOgType(string $ogType) : void
    {
        $this->ogType = $ogType;
    }

    public function getOgType() : string
    {
        return $this->ogType;
    }

    /** @var string */
    private $twitterCardType = 'summary';

    protected function setTwitterCardType(string $twitterCardType) : void
    {
        $this->twitterCardType = $twitterCardType;
    }

    public function getTwitterCardType() : string
    {
        return $this->twitterCardType;
    }
}
