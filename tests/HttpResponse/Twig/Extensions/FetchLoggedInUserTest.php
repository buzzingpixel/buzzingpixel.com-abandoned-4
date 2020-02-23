<?php

declare(strict_types=1);

namespace Tests\HttpResponse\Twig\Extensions;

use App\HttpResponse\Twig\Extensions\FetchLoggedInUser;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use function assert;
use function is_array;

class FetchLoggedInUserTest extends TestCase
{
    public function testGetFunctions() : void
    {
        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::never())
            ->method(self::anything());

        $ext = new FetchLoggedInUser($userApi);

        $return = $ext->getFunctions();

        $twigFunc = $return[0];

        self::assertSame(
            'fetchLoggedInUser',
            $twigFunc->getName()
        );

        $callable = $twigFunc->getCallable();

        assert(is_array($callable));

        self::assertCount(2, $callable);

        self::assertSame($ext, $callable[0]);

        self::assertSame('fetchLoggedInUser', $callable[1]);

        self::assertFalse($twigFunc->needsEnvironment());

        self::assertFalse($twigFunc->needsContext());
    }

    public function testFetchLoggedInUser() : void
    {
        $user = new UserModel();

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        $ext = new FetchLoggedInUser($userApi);

        self::assertSame(
            $user,
            $ext->fetchLoggedInUser()
        );
    }
}
