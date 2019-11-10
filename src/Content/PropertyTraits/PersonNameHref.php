<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

trait PersonNameHref
{
    /** @var string */
    private $personNameHref = '';

    protected function setPersonNameHref(string $personNameHref) : void
    {
        $this->personNameHref = $personNameHref;
    }

    public function getPersonNameHref() : string
    {
        return $this->personNameHref;
    }
}
