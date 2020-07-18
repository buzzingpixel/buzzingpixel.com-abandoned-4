<?php

declare(strict_types=1);

namespace App\Content\Documentation;

use App\Payload\SpecificPayload;

use function array_walk;

class DocumentationVersionPayload extends SpecificPayload
{
    private string $title = '';

    protected function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    private string $slug = '';

    protected function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    private string $version = '';

    protected function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    /** @var DocumentationPagePayload[] */
    private array $pages = [];

    /**
     * @param DocumentationPagePayload[] $pages
     */
    protected function setPages(array $pages): void
    {
        array_walk($pages, [$this, 'addPage']);
    }

    protected function addPage(DocumentationPagePayload $page): void
    {
        $this->pages[$page->getSlug()] = $page;
    }

    /**
     * @return DocumentationPagePayload[]
     */
    public function getPages(): array
    {
        return $this->pages;
    }

    public function getPageBySlug(string $slug): ?DocumentationPagePayload
    {
        return $this->pages[$slug] ?? null;
    }
}
