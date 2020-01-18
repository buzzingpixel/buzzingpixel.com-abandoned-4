<?php

declare(strict_types=1);

namespace App\HttpResponse\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use function array_map;

class PhpFunctions extends AbstractExtension
{
    /**
     * Add whatever PHP functions here that are desired to pass through to Twig
     *
     * @var string[]
     */
    private $functions = [
        'get_class',
        'gettype',
        'uniqid',
    ];

    /**
     * @return TwigFunction[]
     */
    public function getFunctions() : array
    {
        return array_map(
            static function ($phpFunction) {
                /** @psalm-suppress InvalidArgument */
                return new TwigFunction($phpFunction, $phpFunction);
            },
            $this->functions
        );
    }
}
