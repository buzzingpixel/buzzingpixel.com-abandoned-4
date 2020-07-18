<?php

declare(strict_types=1);

namespace App\Software\Services;

use App\Persistence\Software\SoftwareRecord;
use App\Persistence\Software\SoftwareVersionRecord;
use App\Software\Models\SoftwareModel;
use App\Software\Models\SoftwareVersionModel;
use PDO;

use function array_fill;
use function array_map;
use function count;
use function implode;

class DeleteSoftware
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(SoftwareModel $model): void
    {
        $softwareStatement = $this->pdo->prepare(
            'DELETE FROM ' . (new SoftwareRecord())->getTableName() .
            ' WHERE id = :id'
        );

        $softwareStatement->execute([':id' => $model->id]);

        $ids = array_map(
            static function (SoftwareVersionModel $model): string {
                return $model->id;
            },
            $model->versions
        );

        if (count($ids) < 1) {
            return;
        }

        $in = implode(
            ',',
            array_fill(0, count($ids), '?')
        );

        $versionTableName = (new SoftwareVersionRecord())->getTableName();

        $versionStatement = $this->pdo->prepare(
            'DELETE FROM ' . $versionTableName .
            ' WHERE id IN (' . $in . ')'
        );

        $versionStatement->execute($ids);
    }
}
