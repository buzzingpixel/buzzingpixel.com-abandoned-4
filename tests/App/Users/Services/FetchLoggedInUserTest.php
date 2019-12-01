<?php

declare(strict_types=1);

namespace Tests\App\Users\Services;

use App\Payload\Payload;
use App\Persistence\Constants;
use App\Persistence\SaveExistingRecord;
use App\Persistence\Users\UserSessionRecord;
use App\Users\Models\UserModel;
use App\Users\Services\FetchLoggedInUser;
use App\Users\Services\FetchUserById;
use buzzingpixel\cookieapi\interfaces\CookieApiInterface;
use buzzingpixel\cookieapi\interfaces\CookieInterface;
use DateTimeImmutable;
use DateTimeInterface;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Throwable;
use function Safe\strtotime;

class FetchLoggedInUserTest extends TestCase
{
    /** @var FetchLoggedInUser */
    private $service;

    /** @var CookieApiInterface&MockObject */
    private $cookieApi;
    /** @var PDO&MockObject */
    private $pdo;
    /** @var SaveExistingRecord&MockObject */
    private $saveExistingRecord;
    /** @var FetchUserById&MockObject */
    private $fetchUserById;

    public function testWhenNoCookie() : void
    {
        $this->cookieApi->expects(self::once())
            ->method('retrieveCookie')
            ->with(self::equalTo('user_session_token'))
            ->willReturn(null);

        $this->pdo->expects(self::never())
            ->method(self::anything());

        $this->saveExistingRecord->expects(self::never())
            ->method(self::anything());

        $this->fetchUserById->expects(self::never())
            ->method(self::anything());

        self::assertNull(($this->service)());
    }

    public function testWhenNoSessionRecord() : void
    {
        $cookie = $this->createMock(CookieInterface::class);

        $cookie->method('value')->willReturn('FooBarVal');

        $this->cookieApi->expects(self::at(0))
            ->method('retrieveCookie')
            ->with(self::equalTo('user_session_token'))
            ->willReturn($cookie);

        $this->cookieApi->expects(self::at(1))
            ->method('deleteCookie')
            ->with(self::equalTo($cookie));

        $statement = $this->createMock(PDOStatement::class);

        $statement->expects(self::at(0))
            ->method('execute')
            ->with(self::equalTo(
                [':id' => 'FooBarVal']
            ))
            ->willReturn(true);

        $statement->expects(self::at(1))
            ->method('fetchObject')
            ->with(self::equalTo(UserSessionRecord::class))
            ->willReturn(false);

        $this->pdo->expects(self::once())
            ->method('prepare')
            ->with(self::equalTo(
                'SELECT * FROM user_sessions WHERE id=:id'
            ))
            ->willReturn($statement);

        $this->saveExistingRecord->expects(self::never())
            ->method(self::anything());

        $this->fetchUserById->expects(self::never())
            ->method(self::anything());

        self::assertNull(($this->service)());
    }

    /**
     * @throws Throwable
     */
    public function testWhenNoTouchUpdateNeeded() : void
    {
        $cookie = $this->createMock(CookieInterface::class);

        $cookie->method('value')->willReturn('FooBarVal');

        $this->cookieApi->expects(self::once())
            ->method('retrieveCookie')
            ->with(self::equalTo('user_session_token'))
            ->willReturn($cookie);

        $lastTouchedAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('23 hours ago')
        );

        $userSessionRecord = new UserSessionRecord();

        $userSessionRecord->user_id = 'FooBarUserId';

        $userSessionRecord->last_touched_at = $lastTouchedAt->format(
            Constants::POSTGRES_OUTPUT_FORMAT
        );

        $statement = $this->createMock(PDOStatement::class);

        $statement->expects(self::at(0))
            ->method('execute')
            ->with(self::equalTo(
                [':id' => 'FooBarVal']
            ))
            ->willReturn(true);

        $statement->expects(self::at(1))
            ->method('fetchObject')
            ->with(self::equalTo(UserSessionRecord::class))
            ->willReturn($userSessionRecord);

        $this->pdo->expects(self::once())
            ->method('prepare')
            ->with(self::equalTo(
                'SELECT * FROM user_sessions WHERE id=:id'
            ))
            ->willReturn($statement);

        $this->saveExistingRecord->expects(self::never())
            ->method(self::anything());

        $userModel = new UserModel();

        $this->fetchUserById->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo('FooBarUserId'))
            ->willReturn($userModel);

        self::assertSame($userModel, ($this->service)());
    }

    /**
     * @throws Throwable
     */
    public function testWhenTouchUpdateNeeded() : void
    {
        $cookie = $this->createMock(CookieInterface::class);

        $cookie->method('value')->willReturn('FooBarVal');

        $this->cookieApi->expects(self::once())
            ->method('retrieveCookie')
            ->with(self::equalTo('user_session_token'))
            ->willReturn($cookie);

        $lastTouchedAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('25 hours ago')
        );

        $userSessionRecord = new UserSessionRecord();

        $userSessionRecord->user_id = 'FooBarUserId';

        $userSessionRecord->last_touched_at = $lastTouchedAt->format(
            Constants::POSTGRES_OUTPUT_FORMAT
        );

        $statement = $this->createMock(PDOStatement::class);

        $statement->expects(self::at(0))
            ->method('execute')
            ->with(self::equalTo(
                [':id' => 'FooBarVal']
            ))
            ->willReturn(true);

        $statement->expects(self::at(1))
            ->method('fetchObject')
            ->with(self::equalTo(UserSessionRecord::class))
            ->willReturn($userSessionRecord);

        $this->pdo->expects(self::once())
            ->method('prepare')
            ->with(self::equalTo(
                'SELECT * FROM user_sessions WHERE id=:id'
            ))
            ->willReturn($statement);

        $this->saveExistingRecord->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($userSessionRecord))
            ->willReturn(new Payload(Payload::STATUS_UPDATED));

        $userModel = new UserModel();

        $this->fetchUserById->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo('FooBarUserId'))
            ->willReturn($userModel);

        self::assertSame($userModel, ($this->service)());

        $currentTime = new DateTimeImmutable();

        $updatedLastTouchedAt = DateTimeImmutable::createFromFormat(
            DateTimeInterface::ATOM,
            $userSessionRecord->last_touched_at
        );

        self::assertSame(
            $currentTime->format('Y-m-d H:i:s'),
            $updatedLastTouchedAt->format('Y-m-d H:i:s')
        );
    }

    protected function setUp() : void
    {
        $this->cookieApi = $this->createMock(
            CookieApiInterface::class
        );

        $this->pdo = $this->createMock(PDO::class);

        $this->saveExistingRecord = $this->createMock(
            SaveExistingRecord::class
        );

        $this->fetchUserById = $this->createMock(
            FetchUserById::class
        );

        $this->service = new FetchLoggedInUser(
            $this->cookieApi,
            $this->pdo,
            $this->saveExistingRecord,
            $this->fetchUserById
        );
    }
}
