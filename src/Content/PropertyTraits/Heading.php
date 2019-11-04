<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

trait Heading
{
    /** @var string */
    private $heading = '';

    protected function setHeading(string $heading) : void
    {
        $this->heading = $heading;
    }

    public function getHeading() : string
    {
        return $this->heading;
    }
}
