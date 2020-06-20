<?php

declare(strict_types=1);

namespace App\HttpResponse\Twig\Extensions;

use App\Users\UserApi;
use Safe\DateTimeImmutable;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ExpirationYears extends AbstractExtension
{
    private UserApi $userApi;

    public function __construct(UserApi $userApi)
    {
        $this->userApi = $userApi;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions() : array
    {
        return [
            new TwigFunction(
                'expirationYears',
                [$this, 'expirationYears']
            ),
        ];
    }

    /**
     * @return int[]
     */
    public function expirationYears() : array
    {
        $user = $this->userApi->fetchLoggedInUser();

        $currentDate = new DateTimeImmutable();

        if ($user !== null) {
            $currentDate = $currentDate->setTimezone($user->timezone);
        }

        $year = (int) $currentDate->format('Y');

        $years = [];

        for ($i = 0; $i < 10; $i++) {
            $years[] = $year;
            $year++;
        }

        return $years;
    }
}
