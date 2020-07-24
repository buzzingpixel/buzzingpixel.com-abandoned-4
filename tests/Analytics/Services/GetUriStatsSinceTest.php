<?php

declare(strict_types=1);

namespace Tests\Analytics\Services;

use App\Analytics\Services\GetUriStatsSince;
use App\Persistence\Analytics\AnalyticsRecord;
use App\Persistence\Constants;
use App\Persistence\RecordQuery;
use App\Persistence\RecordQueryFactory;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Throwable;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class GetUriStatsSinceTest extends TestCase
{
    public function testWhenNoDateAndNoRecords(): void
    {
        $query = $this->createMock(RecordQuery::class);

        $query->expects(self::never())
            ->method('withWhere');

        $query->expects(self::once())
            ->method('all')
            ->willReturn([]);

        $queryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $queryFactory->method('__invoke')->willReturn($query);

        $service = new GetUriStatsSince($queryFactory);

        self::assertSame([], $service());
    }

    /**
     * @throws Throwable
     */
    public function test(): void
    {
        $date = (new DateTimeImmutable())
            ->setTimezone(new DateTimeZone('UTC'))
            ->modify('-13 years');

        $timeString = $date->format(Constants::POSTGRES_OUTPUT_FORMAT);

        $record1            = new AnalyticsRecord();
        $record1->cookie_id = 'foo-bar';
        $record1->uri       = '/foo/bar';

        $record2            = new AnalyticsRecord();
        $record2->cookie_id = 'bar-baz';
        $record2->uri       = '/bar/baz';

        $record3            = new AnalyticsRecord();
        $record3->cookie_id = 'foo-bar';
        $record3->uri       = '/foo/bar';

        $records = [$record1, $record2, $record3];

        $query = $this->createMock(RecordQuery::class);

        $query->expects(self::once())
            ->method('withWhere')
            ->with(
                self::equalTo('date'),
                self::equalTo($timeString),
                self::equalTo('>')
            )
            ->willReturn($query);

        $query->expects(self::once())
            ->method('all')
            ->willReturn($records);

        $queryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $queryFactory->method('__invoke')->willReturn($query);

        $service = new GetUriStatsSince($queryFactory);

        $models = $service($date);

        self::assertCount(2, $models);

        self::assertSame('/bar/baz', $models[0]->uri);
        self::assertSame(
            1,
            $models[0]->totalVisitorsInTimeRange
        );
        self::assertSame(
            1,
            $models[0]->totalUniqueVisitorsInTimeRange
        );

        self::assertSame('/foo/bar', $models[1]->uri);
        self::assertSame(
            2,
            $models[1]->totalVisitorsInTimeRange
        );
        self::assertSame(
            1,
            $models[1]->totalUniqueVisitorsInTimeRange
        );
    }
}
