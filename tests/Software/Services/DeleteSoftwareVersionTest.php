<?php

declare(strict_types=1);

namespace Tests\Software\Services;

use App\Persistence\Software\SoftwareVersionRecord;
use App\Software\Models\SoftwareVersionModel;
use App\Software\Services\DeleteSoftwareVersion;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class DeleteSoftwareVersionTest extends TestCase
{
    public function test() : void
    {
        $statement = $this->createMock(PDOStatement::class);

        $statement->expects(self::once())
            ->method('execute')
            ->with(self::equalTo([':id' => 'foo-bar-id']))
            ->willReturn(true);

        $table = (new SoftwareVersionRecord())->getTableName();

        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::once())
            ->method('prepare')
            ->with(self::equalTo(
                'DELETE FROM ' . $table . ' WHERE id = :id'
            ))
            ->willReturn($statement);

        $service = new DeleteSoftwareVersion($pdo);

        $versionModel     = new SoftwareVersionModel();
        $versionModel->id = 'foo-bar-id';

        $service($versionModel);
    }
}
