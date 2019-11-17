<?php

declare(strict_types=1);

namespace App\Content\Documentation;

use App\Payload\SpecificPayload;

class DocumentationPagePayload extends SpecificPayload
{
    /** @var string */
    private $title = '';

    protected function setTitle(string $title) : void
    {
        $this->title = $title;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    /** @var string */
    private $slug = '';

    protected function setSlug(string $slug) : void
    {
        $this->slug = $slug;
    }

    public function getSlug() : string
    {
        return $this->slug;
    }

    /** @var DocumentationPageSectionPayload[] */
    private $sections = [];

    /**
     * @param DocumentationPageSectionPayload[] $sections
     */
    protected function setSections(array $sections) : void
    {
        $this->sections = $sections;
    }

    /**
     * @return DocumentationPageSectionPayload[]
     */
    public function getSections() : array
    {
        return $this->sections;
    }
}
