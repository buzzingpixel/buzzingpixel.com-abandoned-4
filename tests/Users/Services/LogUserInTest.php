<?php

declare(strict_types=1);

namespace Tests\App\Users\Services;

use App\Payload\Payload;
use App\Users\Models\UserModel;
use App\Users\Services\CreateUserSession;
use App\Users\Services\LogUserIn;
use App\Users\Services\SaveUser;
use buzzingpixel\cookieapi\CookieApi;
use buzzingpixel\cookieapi\interfaces\CookieInterface;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Throwable;
use const PASSWORD_ARGON2I;
use const PASSWORD_DEFAULT;
use function date;
use function func_get_args;
use function Safe\password_hash;

class LogUserInTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testWhenPasswordDoesNotVerify() : void
    {
        $service = new LogUserIn(
            $this->createMock(SaveUser::class),
            $this->createMock(CreateUserSession::class),
            $this->createMock(CookieApi::class)
        );

        $payload = $service(
            new UserModel(['passwordHash' => 'FakeHash']),
            'FakePass'
        );

        self::assertSame(
            Payload::STATUS_NOT_VALID,
            $payload->getStatus()
        );

        self::assertSame(
            ['message' => 'Your password is invalid'],
            $payload->getResult()
        );
    }

    /**
     * @throws Throwable
     */
    public function testLoginWhenPasswordNeedsRehashAndSessionPayloadError() : void
    {
        $password = 'FooBarPassword';

        $passwordHash = password_hash(
            $password,
            PASSWORD_ARGON2I
        );

        $user = new UserModel(['passwordHash' => $passwordHash]);

        $createSessionPayload = new Payload(Payload::STATUS_NOT_CREATED);

        $service = new LogUserIn(
            $this->mockSaveUser(true),
            $this->mockCreateUserSession(
                $user,
                $createSessionPayload
            ),
            $this->mockCookieApi(false)
        );

        $payload = $service($user, $password);

        self::assertSame(
            Payload::STATUS_ERROR,
            $payload->getStatus()
        );

        self::assertCount(1, $this->saveUserCallArgs);

        /** @var UserModel|null $saveUserModel */
        $saveUserModel = $this->saveUserCallArgs[0];

        self::assertSame($user, $saveUserModel);

        self::assertNotSame($passwordHash, $user->getPasswordHash());
    }

    /**
     * @throws Throwable
     */
    public function testLogIn() : void
    {
        $password = 'FooBarPassword';

        $passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $user = new UserModel(['passwordHash' => $passwordHash]);

        $createSessionPayload = new Payload(
            Payload::STATUS_CREATED,
            ['id' => 'FooBarId']
        );

        $service = new LogUserIn(
            $this->mockSaveUser(false),
            $this->mockCreateUserSession(
                $user,
                $createSessionPayload
            ),
            $this->mockCookieApi(true)
        );

        $payload = $service($user, $password);

        self::assertSame(
            Payload::STATUS_SUCCESSFUL,
            $payload->getStatus()
        );

        self::assertSame(
            ['id' => 'FooBarId'],
            $payload->getResult()
        );

        self::assertCount(7, $this->makeCookieCallArgs);

        self::assertSame(
            'user_session_token',
            $this->makeCookieCallArgs[0]
        );

        self::assertSame(
            'FooBarId',
            $this->makeCookieCallArgs[1]
        );

        /** @var DateTimeImmutable|null $cookieDateTime */
        $cookieDateTime = $this->makeCookieCallArgs[2];

        self::assertInstanceOf(DateTimeImmutable::class, $cookieDateTime);

        $currentYear = (int) date('Y');

        $yearPlus20 = (string) ($currentYear + 20);

        self::assertSame(
            $yearPlus20,
            $cookieDateTime->format('Y')
        );

        self::assertSame(
            '/',
            $this->makeCookieCallArgs[3]
        );

        self::assertSame(
            '',
            $this->makeCookieCallArgs[4]
        );

        self::assertFalse($this->makeCookieCallArgs[5]);

        self::assertTrue($this->makeCookieCallArgs[6]);

        self::assertCount(1, $this->saveCookieCallArgs);

        /** @var CookieInterface|null $saveCookieCookie */
        $saveCookieCookie = $this->saveCookieCallArgs[0];

        self::assertInstanceOf(CookieInterface::class, $saveCookieCookie);

        self::assertSame('FooBarCookieName', $saveCookieCookie->name());
    }

    /**
     * @return SaveUser&MockObject
     */
    private function mockSaveUser(bool $expectSave = false) : SaveUser
    {
        $this->saveUserCallArgs = [];

        $mock = $this->createMock(SaveUser::class);

        if (! $expectSave) {
            $mock->expects(self::never())
                ->method(self::anything());

            return $mock;
        }

        $mock->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback([$this, 'saveUserCallback']);

        return $mock;
    }

    /** @var mixed[] */
    private $saveUserCallArgs = [];

    public function saveUserCallback() : Payload
    {
        $this->saveUserCallArgs = func_get_args();

        return new Payload(Payload::STATUS_SUCCESSFUL);
    }

    /**
     * @return CreateUserSession&MockObject
     */
    private function mockCreateUserSession(
        UserModel $expectUser,
        Payload $returnPayload
    ) : CreateUserSession {
        $mock = $this->createMock(CreateUserSession::class);

        $mock->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($expectUser))
            ->willReturn($returnPayload);

        return $mock;
    }

    /**
     * @return CookieApi&MockObject
     */
    private function mockCookieApi(bool $expectSave) : CookieApi
    {
        $this->makeCookieCallArgs = [];

        $this->saveCookieCallArgs = [];

        $mock = $this->createMock(CookieApi::class);

        if (! $expectSave) {
            $mock->expects(self::never())
                ->method(self::anything());

            return $mock;
        }

        $mock->expects(self::once())
            ->method('makeCookie')
            ->willReturnCallback([$this, 'makeCookieCallback']);

        $mock->expects(self::once())
            ->method('saveCookie')
            ->willReturnCallback([$this, 'saveCookieCallback']);

        return $mock;
    }

    /** @var mixed[] */
    private $makeCookieCallArgs = [];

    public function makeCookieCallback() : CookieInterface
    {
        $this->makeCookieCallArgs = func_get_args();

        $cookie = $this->createMock(CookieInterface::class);

        $cookie->method('name')->willReturn('FooBarCookieName');

        return $cookie;
    }

    /** @var mixed[] */
    private $saveCookieCallArgs = [];

    public function saveCookieCallback() : void
    {
        $this->saveCookieCallArgs = func_get_args();
    }
}
