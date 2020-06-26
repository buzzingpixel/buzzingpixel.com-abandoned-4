<?php

declare(strict_types=1);

namespace Tests\HttpResponse\Twig\Extensions;

use App\HttpResponse\Twig\Extensions\ExpirationYears;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;

use function assert;
use function is_array;

class ExpirationYearsTest extends TestCase
{
    public function testGetFunctions(): void
    {
        $ext = new ExpirationYears(
            $this->createMock(UserApi::class),
        );

        $return = $ext->getFunctions();

        self::assertCount(1, $return);

        $twigFunc = $return[0];

        self::assertSame(
            'expirationYears',
            $twigFunc->getName()
        );

        $callable = $twigFunc->getCallable();

        assert(is_array($callable));

        self::assertCount(2, $callable);

        self::assertSame($ext, $callable[0]);

        self::assertSame('expirationYears', $callable[1]);

        self::assertFalse($twigFunc->needsEnvironment());

        self::assertFalse($twigFunc->needsContext());
    }

    public function testExpirationYears(): void
    {
        $currentDate = new DateTimeImmutable();

        $user = new UserModel();

        $user->timezone = $currentDate->getTimezone();

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        ////
        $year  = (int) $currentDate->format('Y');
        $years = [];
        for ($i = 0; $i < 10; $i++) {
            $years[] = $year;
            $year++;
        }

        ////

        $ext = new ExpirationYears($userApi);

        self::assertSame(
            $years,
            $ext->expirationYears(),
        );
    }
}
