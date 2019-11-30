<?php

declare(strict_types=1);

namespace Tests\App\Users\Services;

use App\Payload\Payload;
use App\Users\Models\UserModel;
use App\Users\Services\DeleteUser;
use Exception;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DeleteUserTest extends TestCase
{
    /** @var DeleteUser */
    private $service;

    /** @var PDO&MockObject */
    private $pdo;
    /** @var UserModel */
    private $user;

    public function testWhenPdoThrowsException() : void
    {
        $this->pdo->expects(self::at(0))
            ->method('beginTransaction')
            ->willReturn(true);

        $this->pdo->expects(self::at(1))
            ->method('prepare')
            ->with(self::equalTo(
                'DELETE FROM users WHERE guid=:id'
            ))
            ->willThrowException(new Exception());

        $this->pdo->expects(self::at(2))
            ->method('rollBack');

        $payload = ($this->service)($this->user);

        self::assertSame(
            Payload::STATUS_ERROR,
            $payload->getStatus()
        );

        self::assertSame(
            ['message' => 'An unknown error occurred'],
            $payload->getResult()
        );
    }

    public function test() : void
    {
        $this->pdo->expects(self::at(0))
            ->method('beginTransaction');

        /**
         * Delete user
         */

        $statementForDeleteUser = $this->createMock(
            PDOStatement::class
        );

        $statementForDeleteUser->expects(self::once())
            ->method('execute')
            ->with(self::equalTo(
                [':id' => 'TestId']
            ))
            ->willReturn(true);

        $this->pdo->expects(self::at(1))
            ->method('prepare')
            ->with(self::equalTo(
                'DELETE FROM users WHERE guid=:id'
            ))
            ->willReturn($statementForDeleteUser);

        /**
         * Delete session
         */

        $statementForDeleteSession = $this->createMock(
            PDOStatement::class
        );

        $statementForDeleteSession->expects(self::once())
            ->method('execute')
            ->with(self::equalTo(
                [':user_id' => 'TestId']
            ))
            ->willReturn(true);

        $this->pdo->expects(self::at(2))
            ->method('prepare')
            ->with(self::equalTo(
                'DELETE FROM user_sessions WHERE guid=:user_id'
            ))
            ->willReturn($statementForDeleteSession);

        /**
         * Delete tokens
         */

        $statementForDeleteTokens = $this->createMock(
            PDOStatement::class
        );

        $statementForDeleteTokens->expects(self::once())
            ->method('execute')
            ->with(self::equalTo(
                [':user_id' => 'TestId']
            ))
            ->willReturn(true);

        $this->pdo->expects(self::at(3))
            ->method('prepare')
            ->with(self::equalTo(
                'DELETE FROM user_password_reset_tokens WHERE guid=:user_id'
            ))
            ->willReturn($statementForDeleteTokens);

        /**
         * Commit
         */

        $this->pdo->expects(self::at(4))
            ->method('commit')
            ->willReturn(true);

        $payload = ($this->service)($this->user);

        self::assertSame(
            Payload::STATUS_SUCCESSFUL,
            $payload->getStatus()
        );
    }

    protected function setUp() : void
    {
        $this->pdo = $this->createMock(PDO::class);

        $this->user = new UserModel(['id' => 'TestId']);

        $this->service = new DeleteUser($this->pdo);
    }
}
