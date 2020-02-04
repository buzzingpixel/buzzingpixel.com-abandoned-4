<?php

declare(strict_types=1);

namespace App\HttpResponse\Twig\Extensions;

use League\ISO3166\ISO3166;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Countries extends AbstractExtension
{
    /** @var ISO3166 */
    private $ISO3166;

    public function __construct(ISO3166 $ISO3166)
    {
        $this->ISO3166 = $ISO3166;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions() : array
    {
        return [$this->getFunction()];
    }

    public function getFunction() : TwigFunction
    {
        return new TwigFunction(
            'countries',
            [$this, 'countries']
        );
    }

    /**
     * @return mixed[]
     */
    public function countries() : array
    {
        return $this->ISO3166->all();
    }
}
