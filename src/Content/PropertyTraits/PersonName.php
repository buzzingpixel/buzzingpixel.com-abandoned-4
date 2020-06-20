<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

trait PersonName
{
    private string $personName = '';

    protected function setPersonName(string $personName): void
    {
        $this->personName = $personName;
    }

    public function getPersonName(): string
    {
        return $this->personName;
    }
}
