<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

use InvalidArgumentException;
use function is_string;

trait TextBullets
{
    /** @var string[] */
    private $textBullets = [];

    /**
     * @param string[] $textBullets
     */
    protected function setTextBullets(array $textBullets) : void
    {
        foreach ($textBullets as $bullet) {
            if (is_string($bullet)) {
                continue;
            }

            throw new InvalidArgumentException(
                'Bullet must be a string'
            );
        }

        $this->textBullets = $textBullets;
    }

    /**
     * @return string[]
     */
    public function getTextBullets() : array
    {
        return $this->textBullets;
    }
}
