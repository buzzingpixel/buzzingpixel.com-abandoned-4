<?php

declare(strict_types=1);

namespace App\Content\Software;

use App\Content\Modules\Payloads\CtaPayload;
use App\Payload\SpecificPayload;
use InvalidArgumentException;

class SoftwareInfoPayload extends SpecificPayload
{
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
        foreach ($actionButtons as $actionButton) {
            if ($actionButton instanceof CtaPayload) {
                continue;
            }

            throw new InvalidArgumentException(
                'Action button must be instance of ' . CtaPayload::class
            );
        }

        $this->actionButtons = $actionButtons;
    }

    /**
     * @return CtaPayload[]
     */
    public function getActionButtons() : array
    {
        return $this->actionButtons;
    }
}
