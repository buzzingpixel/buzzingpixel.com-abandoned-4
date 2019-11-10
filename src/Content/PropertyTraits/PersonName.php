<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

trait PersonName
{
    /** @var string */
    private $personName = '';

    protected function setPersonName(string $personName) : void
    {
        $this->personName = $personName;
    }

    public function getPersonName() : string
    {
        return $this->personName;
    }
}
