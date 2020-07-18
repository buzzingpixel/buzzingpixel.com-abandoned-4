<?php

declare(strict_types=1);

namespace Tests\Users\Services;

use App\Persistence\RecordQuery;
use App\Persistence\RecordQueryFactory;
use App\Persistence\UserCards\UserCardRecord;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use App\Users\Services\FetchUserCards;
use App\Users\Transformers\TransformUserCardRecordToModel;
use PHPUnit\Framework\TestCase;

class FetchUserCardsTest extends TestCase
{
    public function test() : void
    {
        $user     = new UserModel();
        $user->id = 'foo-user-id';

        $record     = new UserCardRecord();
        $record->id = 'foo-id';

        $recordQuery = $this->createMock(RecordQuery::class);

        $recordQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('user_id'),
                self::equalTo('foo-user-id')
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(1))
            ->method('withOrder')
            ->with(
                self::equalTo('is_default'),
                self::equalTo('desc')
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(2))
            ->method('withOrder')
            ->with(
                self::equalTo('nickname'),
                self::equalTo('asc')
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(3))
            ->method('all')
            ->willReturn([$record]);

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

        $service = new FetchUserCards(
            $recordQueryFactory,
            $transformer
        );

        self::assertSame(
            [$model],
            $service($user)
        );
    }
}
