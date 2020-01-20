<?php

declare(strict_types=1);

namespace App\Content\Software;

use App\Content\Modules\Payloads\CtaPayload;
use App\Payload\SpecificPayload;
use function array_walk;

class SoftwareInfoPayload extends SpecificPayload
{
    /** @var string */
    private $slug = '';

    protected function setSlug(string $slug) : void
    {
        $this->slug = $slug;
    }

    public function getSlug() : string
    {
        return $this->slug;
    }

    /** @var string */
    private $title = '';

    protected function setTitle(string $title) : void
    {
        $this->title = $title;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    /** @var string */
    private $subTitle = '';

    protected function setSubTitle(string $subTitle) : void
    {
        $this->subTitle = $subTitle;
    }

    public function getSubTitle() : string
    {
        return $this->subTitle;
    }

    /** @var bool */
    private $forSale = false;

    protected function setForSale(bool $forSale) : void
    {
        $this->forSale = $forSale;
    }

    public function getForSale() : bool
    {
        return $this->forSale;
    }

    /** @var bool */
    private $hasChangelog = false;

    protected function setHasChangelog(bool $hasChangelog) : void
    {
        $this->hasChangelog = $hasChangelog;
    }

    public function getHasChangelog() : bool
    {
        return $this->hasChangelog;
    }

    /** @var string */
    private $changelogExternalUrl = '';

    protected function setChangelogExternalUrl(string $changelogExternalUrl) : void
    {
        $this->changelogExternalUrl = $changelogExternalUrl;
    }

    public function getChangelogExternalUrl() : string
    {
        return $this->changelogExternalUrl;
    }

    /** @var bool */
    private $hasDocumentation = false;

    protected function setHasDocumentation(bool $hasDocumentation) : void
    {
        $this->hasDocumentation = $hasDocumentation;
    }

    public function getHasDocumentation() : bool
    {
        return $this->hasDocumentation;
    }

    /** @var CtaPayload[] */
    private $actionButtons = [];

    /**
     * @param CtaPayload[] $actionButtons
     */
    protected function setActionButtons(array $actionButtons) : void
    {
        array_walk($actionButtons, [$this, 'setActionButton']);
    }

    protected function setActionButton(CtaPayload $ctaPayload) : void
    {
        $this->actionButtons[] = $ctaPayload;
    }

    /**
     * @return CtaPayload[]
     */
    public function getActionButtons() : array
    {
        return $this->actionButtons;
    }
}
