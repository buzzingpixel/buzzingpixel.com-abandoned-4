<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

trait PersonNameHref
{
    private string $personNameHref = '';

    protected function setPersonNameHref(string $personNameHref) : void
    {
        $this->personNameHref = $personNameHref;
    }

    public function getPersonNameHref() : string
    {
        return $this->personNameHref;
    }
}
