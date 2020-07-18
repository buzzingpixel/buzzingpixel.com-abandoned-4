<?php

declare(strict_types=1);

namespace Tests\Users\Services;

use App\Payload\Payload;
use App\Persistence\UserCards\UserCardRecord;
use App\Users\Events\DeleteUserCardAfterDelete;
use App\Users\Events\DeleteUserCardBeforeDelete;
use App\Users\Models\UserCardModel;
use App\Users\Services\DeleteUserCard;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class DeleteUserCardTest extends TestCase
{
    public function testInvalidBeforeEvent() : void
    {
        $card = new UserCardModel();

        $eventDispatcher = $this->createMock(
            EventDispatcherInterface::class,
        );

        $eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->willReturnCallback(
                static function (
                    DeleteUserCardBeforeDelete $beforeDelete
                ) : void {
                    $beforeDelete->isValid = false;
                }
            );

        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::at(0))
            ->method('beginTransaction');

        $pdo->expects(self::at(1))
            ->method('rollBack');

        $service = new DeleteUserCard(
            $eventDispatcher,
            $pdo
        );

        $payload = $service($card);

        self::assertSame(
            Payload::STATUS_ERROR,
            $payload->getStatus(),
        );
    }

    public function testInvalidExecute() : void
    {
        $card = new UserCardModel();

        $card->id = 'foo-id';

        $eventDispatcher = $this->createMock(
            EventDispatcherInterface::class,
        );

        $eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->willReturnCallback(
                static function (
                    DeleteUserCardBeforeDelete $beforeDelete
                ) : void {
                }
            );

        $statement = $this->createMock(
            PDOStatement::class,
        );

        $statement->expects(self::once())
            ->method('execute')
            ->with(self::equalTo([':id' => $card->id]))
            ->willReturn(false);

        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::at(0))
            ->method('beginTransaction');

        $pdo->expects(self::at(1))
            ->method('prepare')
            ->with(
                self::equalTo(
                    'DELETE FROM ' .
                    UserCardRecord::tableName() .
                    ' WHERE id=:id'
                ),
            )
            ->willReturn($statement);

        $pdo->expects(self::at(2))
            ->method('rollBack');

        $service = new DeleteUserCard(
            $eventDispatcher,
            $pdo
        );

        $payload = $service($card);

        self::assertSame(
            Payload::STATUS_ERROR,
            $payload->getStatus(),
        );
    }

    public function testInvalidAfterEvent() : void
    {
        $card = new UserCardModel();

        $card->id = 'foo-id';

        $eventDispatcher = $this->createMock(
            EventDispatcherInterface::class,
        );

        $eventDispatcher->expects(self::at(0))
            ->method('dispatch')
            ->willReturnCallback(
                static function (
                    DeleteUserCardBeforeDelete $beforeDelete
                ) : void {
                }
            );

        $eventDispatcher->expects(self::at(1))
            ->method('dispatch')
            ->willReturnCallback(
                static function (
                    DeleteUserCardAfterDelete $afterDelete
                ) : void {
                    $afterDelete->isValid = false;
                }
            );

        $statement = $this->createMock(
            PDOStatement::class,
        );

        $statement->expects(self::once())
            ->method('execute')
            ->with(self::equalTo([':id' => $card->id]))
            ->willReturn(true);

        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::at(0))
            ->method('beginTransaction');

        $pdo->expects(self::at(1))
            ->method('prepare')
            ->with(
                self::equalTo(
                    'DELETE FROM ' .
                    UserCardRecord::tableName() .
                    ' WHERE id=:id'
                ),
            )
            ->willReturn($statement);

        $pdo->expects(self::at(2))
            ->method('rollBack');

        $service = new DeleteUserCard(
            $eventDispatcher,
            $pdo
        );

        $payload = $service($card);

        self::assertSame(
            Payload::STATUS_ERROR,
            $payload->getStatus(),
        );
    }

    public function test() : void
    {
        $card = new UserCardModel();

        $card->id = 'foo-id';

        $eventDispatcher = $this->createMock(
            EventDispatcherInterface::class,
        );

        $eventDispatcher->expects(self::at(0))
            ->method('dispatch')
            ->willReturnCallback(
                static function (
                    DeleteUserCardBeforeDelete $beforeDelete
                ) : void {
                }
            );

        $eventDispatcher->expects(self::at(1))
            ->method('dispatch')
            ->willReturnCallback(
                static function (
                    DeleteUserCardAfterDelete $afterDelete
                ) : void {
                }
            );

        $statement = $this->createMock(
            PDOStatement::class,
        );

        $statement->expects(self::once())
            ->method('execute')
            ->with(self::equalTo([':id' => $card->id]))
            ->willReturn(true);

        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::at(0))
            ->method('beginTransaction');

        $pdo->expects(self::at(1))
            ->method('prepare')
            ->with(
                self::equalTo(
                    'DELETE FROM ' .
                    UserCardRecord::tableName() .
                    ' WHERE id=:id'
                ),
            )
            ->willReturn($statement);

        $pdo->expects(self::at(2))
            ->method('commit');

        $service = new DeleteUserCard(
            $eventDispatcher,
            $pdo
        );

        $payload = $service($card);

        self::assertSame(
            Payload::STATUS_DELETED,
            $payload->getStatus(),
        );
    }
}
