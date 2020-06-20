<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

trait Content
{
    private string $content = '';

    protected function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
