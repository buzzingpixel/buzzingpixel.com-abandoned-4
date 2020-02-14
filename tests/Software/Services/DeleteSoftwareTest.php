<?php

declare(strict_types=1);

namespace Tests\Software\Services;

use App\Persistence\Software\SoftwareRecord;
use App\Software\Models\SoftwareModel;
use App\Software\Models\SoftwareVersionModel;
use App\Software\Services\DeleteSoftware;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class DeleteSoftwareTest extends TestCase
{
    public function testWhenNoVersions() : void
    {
        $statement = $this->createMock(PDOStatement::class);

        $statement->expects(self::once())
            ->method('execute')
            ->with(
                self::equalTo([':id' => 'foo-id'])
            )
            ->willReturn(true);

        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::once())
            ->method('prepare')
            ->with(self::equalTo(
                'DELETE FROM ' . (new SoftwareRecord())->getTableName() .
                ' WHERE id = :id'
            ))
            ->willReturn($statement);

        $softwareModel     = new SoftwareModel();
        $softwareModel->id = 'foo-id';

        $service = new DeleteSoftware($pdo);

        $service($softwareModel);
    }

    public function testWithOneVersion() : void
    {
        $statement1 = $this->createMock(PDOStatement::class);

        $statement1->expects(self::once())
            ->method('execute')
            ->with(
                self::equalTo([':id' => 'foo-id'])
            )
            ->willReturn(true);

        $statement2 = $this->createMock(PDOStatement::class);

        $statement2->expects(self::once())
            ->method('execute')
            ->with(
                self::equalTo(['version-id'])
            )
            ->willReturn(true);

        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::at(0))
            ->method('prepare')
            ->with(self::equalTo(
                'DELETE FROM ' . (new SoftwareRecord())->getTableName() .
                ' WHERE id = :id'
            ))
            ->willReturn($statement1);

        $pdo->expects(self::at(1))
            ->method('prepare')
            ->with(self::equalTo(
                'DELETE FROM software_versions WHERE id IN (?)'
            ))
            ->willReturn($statement2);

        $version     = new SoftwareVersionModel();
        $version->id = 'version-id';

        $softwareModel     = new SoftwareModel();
        $softwareModel->id = 'foo-id';
        $softwareModel->addVersion($version);

        $service = new DeleteSoftware($pdo);

        $service($softwareModel);
    }

    public function testWithTwoVersions() : void
    {
        $statement1 = $this->createMock(PDOStatement::class);

        $statement1->expects(self::once())
            ->method('execute')
            ->with(
                self::equalTo([':id' => 'foo-id'])
            )
            ->willReturn(true);

        $statement2 = $this->createMock(PDOStatement::class);

        $statement2->expects(self::once())
            ->method('execute')
            ->with(
                self::equalTo([
                    'version-id-1',
                    'version-id-2',
                ])
            )
            ->willReturn(true);

        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::at(0))
            ->method('prepare')
            ->with(self::equalTo(
                'DELETE FROM ' . (new SoftwareRecord())->getTableName() .
                ' WHERE id = :id'
            ))
            ->willReturn($statement1);

        $pdo->expects(self::at(1))
            ->method('prepare')
            ->with(self::equalTo(
                'DELETE FROM software_versions WHERE id IN (?,?)'
            ))
            ->willReturn($statement2);

        $version1     = new SoftwareVersionModel();
        $version1->id = 'version-id-1';
        $version2     = new SoftwareVersionModel();
        $version2->id = 'version-id-2';

        $softwareModel     = new SoftwareModel();
        $softwareModel->id = 'foo-id';
        $softwareModel->addVersion($version1);
        $softwareModel->addVersion($version2);

        $service = new DeleteSoftware($pdo);

        $service($softwareModel);
    }
}
