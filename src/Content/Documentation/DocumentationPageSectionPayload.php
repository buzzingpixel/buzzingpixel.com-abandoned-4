<?php

declare(strict_types=1);

namespace App\Content\Documentation;

use App\Payload\SpecificPayload;
use function array_walk;

class DocumentationPageSectionPayload extends SpecificPayload
{
    private string $title = '';

    protected function setTitle(string $title) : void
    {
        $this->title = $title;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    /** @var SpecificPayload[] */
    private array $content = [];

    /**
     * @param SpecificPayload[] $content
     */
    protected function setContent(array $content) : void
    {
        array_walk($content, [$this, 'addContent']);
    }

    protected function addContent(SpecificPayload $content) : void
    {
        $this->content[] = $content;
    }

    /**
     * @return SpecificPayload[]
     */
    public function getContent() : array
    {
        return $this->content;
    }
}
