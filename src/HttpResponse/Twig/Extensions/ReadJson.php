<?php

declare(strict_types=1);

namespace App\HttpResponse\Twig\Extensions;

use Throwable;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use function file_get_contents;
use function Safe\json_decode;

class ReadJson extends AbstractExtension
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
            'readJson',
            [$this, 'readJsonFunction']
        );
    }

    /**
     * @return mixed[]
     *
     * @throws Throwable
     */
    public function readJsonFunction(string $filePath) : array
    {
        return json_decode(
            file_get_contents($filePath),
            true
        );
    }
}
