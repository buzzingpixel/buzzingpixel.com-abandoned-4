<?php

declare(strict_types=1);

namespace Tests\HttpResponse\Twig\Extensions;

use App\HttpResponse\Twig\Extensions\Countries;
use League\ISO3166\ISO3166;
use PHPUnit\Framework\TestCase;
use function assert;
use function is_array;

class CountriesTest extends TestCase
{
    public function testGetFunctions() : void
    {
        $ISO3166 = $this->createMock(ISO3166::class);

        $ISO3166->expects(self::never())
            ->method(self::anything());

        $ext = new Countries($ISO3166);

        $return = $ext->getFunctions();

        self::assertCount(2, $return);

        $twigFunc1 = $return[0];

        self::assertSame(
            'countries',
            $twigFunc1->getName()
        );

        $callable1 = $twigFunc1->getCallable();

        assert(is_array($callable1));

        self::assertCount(2, $callable1);

        self::assertSame($ext, $callable1[0]);

        self::assertSame('countries', $callable1[1]);

        self::assertFalse($twigFunc1->needsEnvironment());

        self::assertFalse($twigFunc1->needsContext());

        $twigFunc2 = $return[1];

        self::assertSame(
            'countriesSelectArray',
            $twigFunc2->getName()
        );

        $callable2 = $twigFunc2->getCallable();

        assert(is_array($callable2));

        self::assertCount(2, $callable2);

        self::assertSame($ext, $callable2[0]);

        self::assertSame('countriesSelectArray', $callable2[1]);

        self::assertFalse($twigFunc2->needsEnvironment());

        self::assertFalse($twigFunc2->needsContext());
    }

    public function testCountries() : void
    {
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

    public function testCountriesSelect() : void
    {
        $ISO3166 = $this->createMock(ISO3166::class);

        $ISO3166->expects(self::once())
            ->method('all')
            ->willReturn([
                [
                    'alpha2' => 'foo-alpha-2-1',
                    'name' => 'foo-name-1',
                ],
                [
                    'alpha2' => 'foo-alpha-2-2',
                    'name' => 'foo-name-2',
                ],
            ]);

        $ext = new Countries($ISO3166);

        self::assertSame(
            [
                'foo-alpha-2-1' => 'foo-name-1',
                'foo-alpha-2-2' => 'foo-name-2',
            ],
            $ext->countriesSelectArray(),
        );
    }
}
