<?php

declare(strict_types=1);

namespace Tests\Analytics\Services;

use App\Analytics\Services\GetTotalPageViewsSince;
use App\Persistence\Analytics\AnalyticsRecord;
use App\Persistence\Constants;
use DateTimeZone;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Throwable;

class GetTotalPageViewsSinceTest extends TestCase
{
    public function testWhenNoDate(): void
    {
        $table = AnalyticsRecord::tableName();

        $qs = 'SELECT count(cookie_id) FROM ' . $table;

        $pdoStatement = $this->createMock(
            PDOStatement::class
        );

        $pdoStatement->expects(self::once())
            ->method('fetchColumn')
            ->willReturn('3');

        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::once())
            ->method('query')
            ->with(self::equalTo($qs))
            ->willReturn($pdoStatement);

        $service = new GetTotalPageViewsSince($pdo);

        self::assertSame(3, $service());
    }

    /**
     * @throws Throwable
     */
    public function test(): void
    {
        $date = (new DateTimeImmutable())
            ->setTimezone(new DateTimeZone('UTC'))
            ->modify('-24 hours');

        $timeString = $date->format(Constants::POSTGRES_OUTPUT_FORMAT);

        $table = AnalyticsRecord::tableName();

        $qs = 'SELECT count(cookie_id) FROM ' . $table;

        $qs .= ' WHERE date > ?';

        $pdoStatement = $this->createMock(
            PDOStatement::class
        );

        $pdoStatement->expects(self::at(0))
            ->method('execute')
            ->with(self::equalTo([$timeString]));

        $pdoStatement->expects(self::at(1))
            ->method('fetchColumn')
            ->willReturn('164');

        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::once())
            ->method('prepare')
            ->with(self::equalTo($qs))
            ->willReturn($pdoStatement);

        $service = new GetTotalPageViewsSince($pdo);

        self::assertSame(164, $service($date));
    }
}
