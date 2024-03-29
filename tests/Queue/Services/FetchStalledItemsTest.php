<?php

declare(strict_types=1);

namespace Tests\Queue\Services;

use App\Persistence\Queue\QueueRecord;
use App\Persistence\RecordQuery;
use App\Persistence\RecordQueryFactory;
use App\Queue\Models\QueueModel;
use App\Queue\Services\FetchHelper;
use App\Queue\Services\FetchStalledItems;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use function assert;

class FetchStalledItemsTest extends TestCase
{
    public function test() : void
    {
        $record = new QueueRecord();

        $model = new QueueModel();

        $recordQuery = $this->createMock(RecordQuery::class);

        $recordQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('finished_due_to_error'),
                self::equalTo('1'),
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(1))
            ->method('withOrder')
            ->with(
                self::equalTo('added_at'),
                self::equalTo('asc'),
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(2))
            ->method('all')
            ->willReturn([$record]);

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        assert(
            $recordQueryFactory instanceof RecordQueryFactory &&
            $recordQueryFactory instanceof MockObject
        );

        $recordQueryFactory->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo(new QueueRecord()))
            ->willReturn($recordQuery);

        $fetchHelper = $this->createMock(
            FetchHelper::class
        );

        $fetchHelper->expects(self::once())
            ->method('processRecords')
            ->with(self::equalTo([$record]))
            ->willReturn([$model]);

        assert(
            $fetchHelper instanceof FetchHelper &&
            $fetchHelper instanceof MockObject
        );

        $service = new FetchStalledItems(
            $fetchHelper,
            $recordQueryFactory
        );

        self::assertSame([$model], $service());
    }
}
