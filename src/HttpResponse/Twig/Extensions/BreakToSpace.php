<?php

declare(strict_types=1);

namespace App\HttpResponse\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use function preg_replace;
use function trim;

class BreakToSpace extends AbstractExtension
{
    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [$this->getFunction()];
    }

    private function getFunction() : TwigFunction
    {
        return new TwigFunction(
            'breakToSpace',
            [$this, 'breakToSpaceMethod']
        );
    }

    public function breakToSpaceMethod(string $classes) : string
    {
        return trim(preg_replace(
            '/\s\s+/',
            ' ',
            $classes
        ));
    }
}
