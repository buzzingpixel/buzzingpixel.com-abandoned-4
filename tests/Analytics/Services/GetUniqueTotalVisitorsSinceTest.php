<?php

declare(strict_types=1);

namespace Tests\Analytics\Services;

use App\Analytics\Services\GetUniqueTotalVisitorsSince;
use App\Persistence\Analytics\AnalyticsRecord;
use App\Persistence\Constants;
use DateTimeZone;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Throwable;

class GetUniqueTotalVisitorsSinceTest extends TestCase
{
    public function testWhenNoDate(): void
    {
        $table = AnalyticsRecord::tableName();

        $qs = 'SELECT count(DISTINCT cookie_id) FROM ' . $table;

        $pdoStatement = $this->createMock(
            PDOStatement::class
        );

        $pdoStatement->expects(self::once())
            ->method('fetchColumn')
            ->willReturn('34');

        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::once())
            ->method('query')
            ->with(self::equalTo($qs))
            ->willReturn($pdoStatement);

        $service = new GetUniqueTotalVisitorsSince($pdo);

        self::assertSame(34, $service());
    }

    /**
     * @throws Throwable
     */
    public function test(): void
    {
        $date = (new DateTimeImmutable())
            ->setTimezone(new DateTimeZone('UTC'))
            ->modify('-13 days');

        $timeString = $date->format(Constants::POSTGRES_OUTPUT_FORMAT);

        $table = AnalyticsRecord::tableName();

        $qs = 'SELECT count(DISTINCT cookie_id) FROM ' . $table;

        $qs .= ' WHERE date > ?';

        $pdoStatement = $this->createMock(
            PDOStatement::class
        );

        $pdoStatement->expects(self::at(0))
            ->method('execute')
            ->with(self::equalTo([$timeString]));

        $pdoStatement->expects(self::at(1))
            ->method('fetchColumn')
            ->willReturn('365');

        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::once())
            ->method('prepare')
            ->with(self::equalTo($qs))
            ->willReturn($pdoStatement);

        $service = new GetUniqueTotalVisitorsSince($pdo);

        self::assertSame(365, $service($date));
    }
}
