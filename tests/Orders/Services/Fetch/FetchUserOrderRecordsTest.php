<?php

declare(strict_types=1);

namespace Tests\Orders\Services\Fetch;

use App\Orders\Services\Fetch\FetchUserOrderRecords;
use App\Persistence\Orders\OrderRecord;
use App\Persistence\RecordQuery;
use App\Persistence\RecordQueryFactory;
use App\Users\Models\UserModel;
use PHPUnit\Framework\TestCase;

class FetchUserOrderRecordsTest extends TestCase
{
    public function test() : void
    {
        $records = [
            new OrderRecord(),
            new OrderRecord(),
        ];

        $user     = new UserModel();
        $user->id = 'foo-user-id';

        $recordQuery = $this->createMock(RecordQuery::class);

        $recordQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('user_id'),
                self::equalTo($user->id),
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(1))
            ->method('withOrder')
            ->with(
                self::equalTo('date'),
                self::equalTo('desc'),
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(2))
            ->method('all')
            ->willReturn($records);

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static fn(OrderRecord $record) => $recordQuery,
            );

        $service = new FetchUserOrderRecords(
            $recordQueryFactory
        );

        self::assertSame(
            $records,
            $service($user),
        );
    }
}
