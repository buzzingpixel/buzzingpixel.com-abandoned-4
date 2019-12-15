<?php

declare(strict_types=1);

namespace App\Software\Services;

use App\Persistence\Software\SoftwareVersionRecord;
use App\Software\Models\SoftwareVersionModel;
use PDO;

class DeleteSoftwareVersion
{
    /** @var PDO */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(SoftwareVersionModel $model) : void
    {
        $table = (new SoftwareVersionRecord())->getTableName();

        $statement = $this->pdo->prepare(
            'DELETE FROM ' . $table . ' WHERE id = :id'
        );

        $statement->execute([':id' => $model->getId()]);
    }
}
