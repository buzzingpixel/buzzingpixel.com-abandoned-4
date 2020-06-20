<?php

declare(strict_types=1);

namespace App\Content\Meta;

use App\Payload\SpecificPayload;

class MetaPayload extends SpecificPayload
{
    private bool $noIndex = false;

    protected function setNoIndex(bool $noIndex): void
    {
        $this->noIndex = $noIndex;
    }

    public function getNoIndex(): bool
    {
        return $this->noIndex;
    }

    private string $metaTitle = '';

    protected function setMetaTitle(string $metaTitle): void
    {
        $this->metaTitle = $metaTitle;
    }

    public function getMetaTitle(): string
    {
        return $this->metaTitle;
    }

    public function withMetaTitle(string $newMetaTitle): MetaPayload
    {
        $newMetaPayload = clone $this;

        $newMetaPayload->metaTitle = $newMetaTitle;

        return $newMetaPayload;
    }

    private string $metaDescription = '';

    protected function setMetaDescription(string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    public function getMetaDescription(): string
    {
        return $this->metaDescription;
    }

    public function withMetaDescription(string $newMetaDescription): MetaPayload
    {
        $newMetaPayload = clone $this;

        $newMetaPayload->metaDescription = $newMetaDescription;

        return $newMetaPayload;
    }

    private string $ogType = 'website';

    protected function setOgType(string $ogType): void
    {
        $this->ogType = $ogType;
    }

    public function getOgType(): string
    {
        return $this->ogType;
    }

    private string $twitterCardType = 'summary';

    protected function setTwitterCardType(string $twitterCardType): void
    {
        $this->twitterCardType = $twitterCardType;
    }

    public function getTwitterCardType(): string
    {
        return $this->twitterCardType;
    }

    private ?HeadingBackgroundPayload $headingBackground = null;

    protected function setHeadingBackground(?HeadingBackgroundPayload $headingBackground): void
    {
        $this->headingBackground = $headingBackground;
    }

    public function getHeadingBackground(): ?HeadingBackgroundPayload
    {
        return $this->headingBackground;
    }
}
