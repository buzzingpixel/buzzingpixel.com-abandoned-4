<?php

declare(strict_types=1);

namespace Tests\Users\EventListeners;

use App\Persistence\RecordQuery;
use App\Persistence\RecordQueryFactory;
use App\Persistence\UserCards\UserCardRecord;
use App\Users\EventListeners\UserCardBeforeSaveSetDefault;
use App\Users\Events\SaveUserCardBeforeSave;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class UserCardBeforeSaveSetDefaultTest extends TestCase
{
    public function testWhenIsDefault(): void
    {
        $user = new UserModel();

        $user->id = 'foo-user-id';

        $cardModel = new UserCardModel();

        $cardModel->id = 'foo-card-id';

        $cardModel->user = $user;

        $cardModel->isDefault = true;

        $beforeSave = new SaveUserCardBeforeSave($cardModel);

        $t = UserCardRecord::tableName();

        $pdoStatement = $this->createMock(
            PDOStatement::class
        );

        $pdoStatement->expects(self::once())
            ->method('execute')
            ->with(self::equalTo([
                ':id' => $cardModel->id,
            ]))
            ->willReturn(true);

        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::once())
            ->method('prepare')
            ->with(
                self::equalTo(
                    'UPDATE ' . $t . ' SET is_default = false WHERE id != :id'
                ),
            )
            ->willReturn($pdoStatement);

        $queryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $queryFactory->expects(self::never())
            ->method(self::anything());

        $listener = new UserCardBeforeSaveSetDefault(
            $pdo,
            $queryFactory,
        );

        $listener->onBeforeSaveUserCard($beforeSave);
    }

    public function testWhenNotDefaultAndDefaultExists(): void
    {
        $user = new UserModel();

        $user->id = 'foo-user-id';

        $cardModel = new UserCardModel();

        $cardModel->id = 'foo-card-id';

        $cardModel->user = $user;

        $beforeSave = new SaveUserCardBeforeSave($cardModel);

        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::never())
            ->method(self::anything());

        $record = new UserCardRecord();

        $query = $this->createMock(RecordQuery::class);

        $query->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('is_default'),
                self::equalTo('1'),
            )
            ->willReturn($query);

        $query->expects(self::at(1))
            ->method('withWhere')
            ->with(
                self::equalTo('user_id'),
                self::equalTo($user->id),
            )
            ->willReturn($query);

        $query->expects(self::at(2))
            ->method('one')
            ->willReturn($record);

        $queryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $queryFactory->expects(self::once())
            ->method('__invoke')
            ->with(new UserCardRecord())
            ->willReturn($query);

        $listener = new UserCardBeforeSaveSetDefault(
            $pdo,
            $queryFactory,
        );

        $listener->onBeforeSaveUserCard($beforeSave);

        self::assertFalse($cardModel->isDefault);
    }

    public function testWhenNotDefaultAndNoDefaultExists(): void
    {
        $user = new UserModel();

        $user->id = 'foo-user-id';

        $cardModel = new UserCardModel();

        $cardModel->id = 'foo-card-id';

        $cardModel->user = $user;

        $beforeSave = new SaveUserCardBeforeSave($cardModel);

        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::never())
            ->method(self::anything());

        $query = $this->createMock(RecordQuery::class);

        $query->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('is_default'),
                self::equalTo('1'),
            )
            ->willReturn($query);

        $query->expects(self::at(1))
            ->method('withWhere')
            ->with(
                self::equalTo('user_id'),
                self::equalTo($user->id),
            )
            ->willReturn($query);

        $query->expects(self::at(2))
            ->method('one')
            ->willReturn(null);

        $queryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $queryFactory->expects(self::once())
            ->method('__invoke')
            ->with(new UserCardRecord())
            ->willReturn($query);

        $listener = new UserCardBeforeSaveSetDefault(
            $pdo,
            $queryFactory,
        );

        $listener->onBeforeSaveUserCard($beforeSave);

        self::assertTrue($cardModel->isDefault);
    }
}
