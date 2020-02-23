<?php

declare(strict_types=1);

namespace Tests\Software\Services;

use App\Persistence\Record;
use App\Persistence\RecordQuery;
use App\Persistence\RecordQueryFactory;
use App\Persistence\Software\SoftwareVersionRecord;
use App\Software\Models\SoftwareModel;
use App\Software\Models\SoftwareVersionModel;
use App\Software\Services\FetchSoftwareById;
use App\Software\Services\FetchSoftwareVersionById;
use Exception;
use PHPUnit\Framework\TestCase;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class FetchSoftwareVersionByIdTest extends TestCase
{
    public function testThrow() : void
    {
        $recordQuery = $this->createMock(RecordQuery::class);

        $recordQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('id'),
                self::equalTo('foo-id')
            )
            ->willThrowException(new Exception());

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (Record $record) use ($recordQuery) {
                    self::assertInstanceOf(
                        SoftwareVersionRecord::class,
                        $record
                    );

                    return $recordQuery;
                }
            );

        $fetchSoftwareById = $this->createMock(
            FetchSoftwareById::class
        );

        $fetchSoftwareById->expects(self::never())
            ->method(self::anything());

        $service = new FetchSoftwareVersionById(
            $recordQueryFactory,
            $fetchSoftwareById,
        );

        self::assertNull($service('foo-id'));
    }

    public function testWhenNoRecord() : void
    {
        $recordQuery = $this->createMock(RecordQuery::class);

        $recordQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('id'),
                self::equalTo('foo-id')
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(1))
            ->method('one')
            ->willReturn(null);

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (Record $record) use ($recordQuery) {
                    self::assertInstanceOf(
                        SoftwareVersionRecord::class,
                        $record
                    );

                    return $recordQuery;
                }
            );

        $fetchSoftwareById = $this->createMock(
            FetchSoftwareById::class
        );

        $fetchSoftwareById->expects(self::never())
            ->method(self::anything());

        $service = new FetchSoftwareVersionById(
            $recordQueryFactory,
            $fetchSoftwareById,
        );

        self::assertNull($service('foo-id'));
    }

    public function testWhenNoSoftwareRecord() : void
    {
        $versionRecord = new SoftwareVersionRecord();

        $versionRecord->software_id = 'foo-software-id';

        $recordQuery = $this->createMock(RecordQuery::class);

        $recordQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('id'),
                self::equalTo('foo-id')
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(1))
            ->method('one')
            ->willReturn($versionRecord);

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (Record $record) use ($recordQuery) {
                    self::assertInstanceOf(
                        SoftwareVersionRecord::class,
                        $record
                    );

                    return $recordQuery;
                }
            );

        $fetchSoftwareById = $this->createMock(
            FetchSoftwareById::class
        );

        $fetchSoftwareById->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo('foo-software-id'))
            ->willReturn(null);

        $service = new FetchSoftwareVersionById(
            $recordQueryFactory,
            $fetchSoftwareById,
        );

        self::assertNull($service('foo-id'));
    }

    public function testWhenSoftwareVersionsDontMatch() : void
    {
        $versionRecord              = new SoftwareVersionRecord();
        $versionRecord->id          = 'foo-id';
        $versionRecord->software_id = 'foo-software-id';

        $recordQuery = $this->createMock(RecordQuery::class);

        $recordQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('id'),
                self::equalTo('foo-id')
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(1))
            ->method('one')
            ->willReturn($versionRecord);

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (Record $record) use ($recordQuery) {
                    self::assertInstanceOf(
                        SoftwareVersionRecord::class,
                        $record
                    );

                    return $recordQuery;
                }
            );

        $version1     = new SoftwareVersionModel();
        $version1->id = 'foo-1-id';

        $version2     = new SoftwareVersionModel();
        $version2->id = 'foo-2-id';

        $software = new SoftwareModel();
        $software->addVersion($version1);
        $software->addVersion($version2);

        $fetchSoftwareById = $this->createMock(
            FetchSoftwareById::class
        );

        $fetchSoftwareById->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo('foo-software-id'))
            ->willReturn($software);

        $service = new FetchSoftwareVersionById(
            $recordQueryFactory,
            $fetchSoftwareById,
        );

        self::assertNull($service('foo-id'));
    }

    public function test() : void
    {
        $versionRecord              = new SoftwareVersionRecord();
        $versionRecord->id          = 'foo-id';
        $versionRecord->software_id = 'foo-software-id';

        $recordQuery = $this->createMock(RecordQuery::class);

        $recordQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('id'),
                self::equalTo('foo-id')
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(1))
            ->method('one')
            ->willReturn($versionRecord);

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (Record $record) use ($recordQuery) {
                    self::assertInstanceOf(
                        SoftwareVersionRecord::class,
                        $record
                    );

                    return $recordQuery;
                }
            );

        $version1     = new SoftwareVersionModel();
        $version1->id = 'foo-1-id';

        $version2     = new SoftwareVersionModel();
        $version2->id = 'foo-id';

        $software = new SoftwareModel();
        $software->addVersion($version1);
        $software->addVersion($version2);

        $fetchSoftwareById = $this->createMock(
            FetchSoftwareById::class
        );

        $fetchSoftwareById->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo('foo-software-id'))
            ->willReturn($software);

        $service = new FetchSoftwareVersionById(
            $recordQueryFactory,
            $fetchSoftwareById,
        );

        self::assertSame($version2, $service('foo-id'));
    }
}
