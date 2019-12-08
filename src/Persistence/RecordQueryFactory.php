<?php

declare(strict_types=1);

namespace App\Persistence;

use PDO;

class RecordQueryFactory
{
    /** @var PDO */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(Record $recordClass) : RecordQuery
    {
        return new RecordQuery($recordClass, $this->pdo);
    }
}
