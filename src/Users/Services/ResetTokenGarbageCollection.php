<?php

declare(strict_types=1);

namespace App\Users\Services;

use App\Persistence\Constants;
use App\Utilities\SystemClock;
use DateTimeZone;
use PDO;
use Throwable;
use function Safe\strtotime;

class ResetTokenGarbageCollection
{
    /** @var PDO */
    private $pdo;
    /** @var SystemClock */
    private $systemClock;

    public function __construct(PDO $pdo, SystemClock $systemClock)
    {
        $this->pdo         = $pdo;
        $this->systemClock = $systemClock;
    }

    /**
     * @throws Throwable
     */
    public function __invoke() : void
    {
        $datetime = $this->systemClock->getCurrentTime()
            ->setTimestamp(
                strtotime('2 hours ago')
            )
            ->setTimezone(new DateTimeZone('UTC'));

        $format = $datetime->format(Constants::POSTGRES_OUTPUT_FORMAT);

        $statement = $this->pdo->prepare(
            'DELETE FROM user_sessions WHERE last_touched_at < ?'
        );

        $statement->execute([$format]);
    }
}