<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

trait SubHeadline
{
    private string $subHeadline = '';

    protected function setSubHeadline(string $subHeadline) : void
    {
        $this->subHeadline = $subHeadline;
    }

    public function getSubHeadline() : string
    {
        return $this->subHeadline;
    }
}
