<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

trait FooterContent
{
    private string $footerContent = '';

    protected function setFooterContent(string $footerContent) : void
    {
        $this->footerContent = $footerContent;
    }

    public function getFooterContent() : string
    {
        return $this->footerContent;
    }
}
