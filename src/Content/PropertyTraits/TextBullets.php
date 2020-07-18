<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

use function array_walk;

trait TextBullets
{
    /** @var string[] */
    private array $textBullets = [];

    /**
     * @param string[] $textBullets
     */
    protected function setTextBullets(array $textBullets): void
    {
        array_walk($textBullets, [$this, 'addTextBullet']);
    }

    private function addTextBullet(string $textBullet): void
    {
        $this->textBullets[] = $textBullet;
    }

    /**
     * @return string[]
     */
    public function getTextBullets(): array
    {
        return $this->textBullets;
    }
}
