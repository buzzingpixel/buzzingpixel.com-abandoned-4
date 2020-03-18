<?php

declare(strict_types=1);

namespace Tests\Orders\Services\Fetch;

use App\Orders\Services\Fetch\FetchOrderItemRecordsByOrderIds;
use App\Persistence\Orders\OrderItemRecord;
use App\Persistence\RecordQuery;
use App\Persistence\RecordQueryFactory;
use PHPUnit\Framework\TestCase;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class FetchOrderItemRecordsByOrderIdsTest extends TestCase
{
    public function test() : void
    {
        $orderIds = [
            'foo-id-1',
            'foo-id-2',
            'foo-id-3',
        ];

        $record1           = new OrderItemRecord();
        $record1->id       = 'item-id-1';
        $record1->order_id = 'foo-id-1';

        $record2           = new OrderItemRecord();
        $record2->id       = 'item-id-2';
        $record2->order_id = 'foo-id-2';

        $record3           = new OrderItemRecord();
        $record3->id       = 'item-id-3';
        $record3->order_id = 'foo-id-2';

        $records = [
            $record1,
            $record2,
            $record3,
        ];

        $recordQuery = $this->createMock(RecordQuery::class);

        $recordQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('order_id'),
                self::equalTo($orderIds),
                self::equalTo('IN'),
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(1))
            ->method('withOrder')
            ->with(
                self::equalTo('item_title'),
                self::equalTo('desc'),
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(2))
            ->method('withOrder')
            ->with(
                self::equalTo('item_key'),
                self::equalTo('desc'),
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(3))
            ->method('withOrder')
            ->with(
                self::equalTo('major_version'),
                self::equalTo('desc'),
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(4))
            ->method('withOrder')
            ->with(
                self::equalTo('version'),
                self::equalTo('desc'),
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(5))
            ->method('all')
            ->willReturn($records);

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static fn(OrderItemRecord $record) => $recordQuery,
            );

        $service = new FetchOrderItemRecordsByOrderIds(
            $recordQueryFactory
        );

        self::assertSame(
            [
                'foo-id-1' => [$record1],
                'foo-id-2' => [
                    $record2,
                    $record3,
                ],
            ],
            $service($orderIds)
        );
    }
}
