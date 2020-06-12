<?php

declare(strict_types=1);

namespace Tests\Users\Services;

use App\Persistence\RecordQuery;
use App\Persistence\RecordQueryFactory;
use App\Persistence\UserCards\UserCardRecord;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use App\Users\Services\FetchUserCardById;
use App\Users\Transformers\TransformUserCardRecordToModel;
use Exception;
use PHPUnit\Framework\TestCase;

class FetchUserCardByIdTest extends TestCase
{
    public function testThrow() : void
    {
        $user = new UserModel();

        $id = 'foo-id';

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->expects(self::once())
            ->method('__invoke')
            ->willThrowException(new Exception());

        $transformer = $this->createMock(
            TransformUserCardRecordToModel::class
        );

        $transformer->expects(self::never())
            ->method(self::anything());

        $service = new FetchUserCardById(
            $recordQueryFactory,
            $transformer
        );

        self::assertNull($service($user, $id));
    }

    public function testNoRecord() : void
    {
        $user = new UserModel();

        $user->id = 'foo-user-id';

        $id = 'foo-id';

        $recordQuery = $this->createMock(RecordQuery::class);

        $recordQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('user_id'),
                self::equalTo('foo-user-id')
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(1))
            ->method('withWhere')
            ->with(
                self::equalTo('id'),
                self::equalTo('foo-id')
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(2))
            ->method('one')
            ->willReturn(null);

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static fn(UserCardRecord $record) => $recordQuery
            );

        $transformer = $this->createMock(
            TransformUserCardRecordToModel::class
        );

        $transformer->expects(self::never())
            ->method(self::anything());

        $service = new FetchUserCardById(
            $recordQueryFactory,
            $transformer
        );

        self::assertNull($service($user, $id));
    }

    public function test() : void
    {
        $user = new UserModel();

        $user->id = 'foo-user-id';

        $id = 'foo-id';

        $record = new UserCardRecord();

        $recordQuery = $this->createMock(RecordQuery::class);

        $recordQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('user_id'),
                self::equalTo('foo-user-id')
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(1))
            ->method('withWhere')
            ->with(
                self::equalTo('id'),
                self::equalTo('foo-id')
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(2))
            ->method('one')
            ->willReturn($record);

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static fn(UserCardRecord $record) => $recordQuery
            );

        $model = new UserCardModel();

        $transformer = $this->createMock(
            TransformUserCardRecordToModel::class
        );

        $transformer->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo($record),
                self::equalTo($user),
            )
            ->willReturn($model);

        $service = new FetchUserCardById(
            $recordQueryFactory,
            $transformer
        );

        self::assertSame(
            $model,
            $service($user, $id)
        );
    }
}
