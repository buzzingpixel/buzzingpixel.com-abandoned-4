<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

trait Href
{
    private string $href = '';

    protected function setHref(string $href): void
    {
        $this->href = $href;
    }

    public function getHref(): string
    {
        return $this->href;
    }
}
