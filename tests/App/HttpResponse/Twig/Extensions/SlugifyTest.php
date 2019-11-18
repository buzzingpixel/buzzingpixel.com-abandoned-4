<?php

declare(strict_types=1);

namespace Tests\App\HttpResponse\Twig\Extensions;

use App\HttpResponse\Twig\Extensions\Slugify;
use Cocur\Slugify\Slugify as CocurSlugify;
use PHPUnit\Framework\TestCase;
use Tests\TestConfig;
use Twig\TwigFilter;

class SlugifyTest extends TestCase
{
    public function test() : void
    {
        $cocurSlugify = TestConfig::$di->get(CocurSlugify::class);

        $extension = new Slugify($cocurSlugify);

        /** @var TwigFilter[]|mixed $filters */
        $filters = $extension->getFilters();
        self::assertCount(1, $filters);

        $filter = $filters[0];

        self::assertInstanceOf(TwigFilter::class, $filter);

        self::assertSame('slugify', $filter->getName());

        /** @var mixed[] $callable */
        $callable = $filter->getCallable();
        self::assertCount(2, $callable);
        self::assertSame($cocurSlugify, $callable[0]);
        self::assertSame('slugify', $callable[1]);
    }
}
