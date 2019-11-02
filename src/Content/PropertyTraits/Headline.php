<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

trait Headline
{
    /** @var string */
    private $headline = '';

    protected function setHeadline(string $headline) : void
    {
        $this->headline = $headline;
    }

    public function getHeadline() : string
    {
        return $this->headline;
    }
}
