<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

trait Content
{
    /** @var string */
    private $content = '';

    protected function setContent(string $content) : void
    {
        $this->content = $content;
    }

    public function getContent() : string
    {
        return $this->content;
    }
}
