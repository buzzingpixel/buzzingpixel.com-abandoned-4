<?php

declare(strict_types=1);

namespace App\HttpResponse\Twig\Extensions;

use App\Utilities\TimeZoneList as TimeZoneListUtility;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TimeZoneList extends AbstractExtension
{
    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [$this->getFunction()];
    }

    public function getFunction(): TwigFunction
    {
        return new TwigFunction(
            'timeZoneList',
            [$this, 'getList']
        );
    }

    /**
     * @return string[]
     */
    public function getList(): array
    {
        return TimeZoneListUtility::getList();
    }
}
