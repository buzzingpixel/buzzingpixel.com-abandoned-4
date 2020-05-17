<?php

declare(strict_types=1);

namespace App\HttpResponse\Twig\Extensions;

use League\ISO3166\ISO3166;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Countries extends AbstractExtension
{
    private ISO3166 $ISO3166;

    public function __construct(ISO3166 $ISO3166)
    {
        $this->ISO3166 = $ISO3166;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions() : array
    {
        return [
            new TwigFunction(
                'countries',
                [$this, 'countries']
            ),
            new TwigFunction(
                'countriesSelectArray',
                [$this, 'countriesSelectArray']
            ),
        ];
    }

    /**
     * @return mixed[]
     */
    public function countries() : array
    {
        return $this->ISO3166->all();
    }

    /**
     * @return mixed[]
     */
    public function countriesSelectArray() : array
    {
        $array = [];

        foreach ($this->ISO3166->all() as $country) {
            /**
             * @psalm-suppress MixedAssignment
             * @psalm-suppress MixedArrayOffset
             */
            $array[$country['alpha2']] = $country['name'];
        }

        return $array;
    }
}
