<?php

declare(strict_types=1);

namespace App\HttpResponse\Twig\Extensions;

use Cocur\Slugify\Slugify as CocurSlugify;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class Slugify extends AbstractExtension
{
    private CocurSlugify $slugify;

    public function __construct(CocurSlugify $slugify)
    {
        $this->slugify = $slugify;
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        return [$this->getFilter()];
    }

    private function getFilter(): TwigFilter
    {
        return new TwigFilter(
            'slugify',
            [$this->slugify, 'slugify']
        );
    }
}
