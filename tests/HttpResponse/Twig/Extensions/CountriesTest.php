<?php

declare(strict_types=1);

namespace Tests\HttpResponse\Twig\Extensions;

use App\HttpResponse\Twig\Extensions\Countries;
use DG\BypassFinals;
use League\ISO3166\ISO3166;
use PHPUnit\Framework\TestCase;
use function assert;
use function is_array;

class CountriesTest extends TestCase
{
    public function testGetFunctions() : void
    {
        BypassFinals::enable();
        $ISO3166 = $this->createMock(ISO3166::class);

        $ISO3166->expects(self::never())
            ->method(self::anything());

        $ext = new Countries($ISO3166);

        $return = $ext->getFunctions();

        $twigFunc = $return[0];

        self::assertSame(
            'countries',
            $twigFunc->getName()
        );

        $callable = $twigFunc->getCallable();

        assert(is_array($callable));

        self::assertCount(2, $callable);

        self::assertSame($ext, $callable[0]);

        self::assertSame('countries', $callable[1]);

        self::assertFalse($twigFunc->needsEnvironment());

        self::assertFalse($twigFunc->needsContext());
    }

    public function testCountries() : void
    {
        BypassFinals::enable();
        $ISO3166 = $this->createMock(ISO3166::class);

        $ISO3166->expects(self::once())
            ->method('all')
            ->willReturn(['test']);

        $ext = new Countries($ISO3166);

        self::assertSame(
            ['test'],
            $ext->countries()
        );
    }
}
