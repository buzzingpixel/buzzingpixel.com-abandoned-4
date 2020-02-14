<?php

declare(strict_types=1);

namespace Tests\Software\Services;

use App\Persistence\Record;
use App\Persistence\RecordQuery;
use App\Persistence\RecordQueryFactory;
use App\Persistence\Software\SoftwareRecord;
use App\Persistence\Software\SoftwareVersionRecord;
use App\Software\Services\FetchAllSoftware;
use App\Software\Transformers\TransformSoftwareRecordToModel;
use App\Software\Transformers\TransformSoftwareVersionRecordToModel;
use DateTimeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\TestConfig;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class FetchAllSoftwareTest extends TestCase
{
    private FetchAllSoftware $service;

    /** @var SoftwareRecord[] */
    private array $softwareRecordQueryAllReturn = [];

    /** @var RecordQuery&MockObject */
    private $softwareRecordQuery;
    /** @var RecordQueryFactory&MockObject */
    private $recordQueryFactory;

    public function testWhenNoRecords() : void
    {
        $this->internalSetUp();

        self::assertCount(0, ($this->service)());
    }

    public function testWhenNoVersions() : void
    {
        $record1              = new SoftwareRecord();
        $record1->id          = 'id1';
        $record1->slug        = 'foo-name';
        $record1->name        = 'Foo Name';
        $record1->is_for_sale = '1';

        $record2              = new SoftwareRecord();
        $record2->id          = 'id2';
        $record2->slug        = 'bar-name';
        $record2->name        = 'Bar Name';
        $record2->is_for_sale = '0';

        $this->softwareRecordQueryAllReturn = [$record1, $record2];

        $this->internalSetUp();

        $this->recordQueryFactory->expects(self::at(1))
            ->method('__invoke')
            ->willReturnCallback(
                function (Record $record) {
                    self::assertInstanceOf(
                        SoftwareVersionRecord::class,
                        $record
                    );

                    return $this->mockVersionRecordQuery();
                }
            );

        $models = ($this->service)();

        self::assertCount(2, $models);

        $model1 = $models[0];
        self::assertSame('id1', $model1->id);
        self::assertSame('foo-name', $model1->slug);
        self::assertSame('Foo Name', $model1->name);
        self::assertTrue($model1->isForSale);
        self::assertSame([], $model1->versions);

        $model2 = $models[1];
        self::assertSame('id2', $model2->id);
        self::assertSame('bar-name', $model2->slug);
        self::assertSame('Bar Name', $model2->name);
        self::assertFalse($model2->isForSale);
        self::assertSame([], $model2->versions);
    }

    public function testWithVersions() : void
    {
        $record1              = new SoftwareRecord();
        $record1->id          = 'id1';
        $record1->slug        = 'foo-name';
        $record1->name        = 'Foo Name';
        $record1->is_for_sale = '1';

        $record2              = new SoftwareRecord();
        $record2->id          = 'id2';
        $record2->slug        = 'bar-name';
        $record2->name        = 'Bar Name';
        $record2->is_for_sale = '0';

        $this->softwareRecordQueryAllReturn = [$record1, $record2];

        $this->internalSetUp();

        $this->recordQueryFactory->expects(self::at(1))
            ->method('__invoke')
            ->willReturnCallback(
                function (Record $record) {
                    self::assertInstanceOf(
                        SoftwareVersionRecord::class,
                        $record
                    );

                    return $this->mockVersionRecordQuery(
                        $this->mockVersionRecords()
                    );
                }
            );

        $models = ($this->service)();

        self::assertCount(2, $models);

        $model1 = $models[0];
        self::assertSame('id1', $model1->id);
        self::assertSame('foo-name', $model1->slug);
        self::assertSame('Foo Name', $model1->name);
        self::assertTrue($model1->isForSale);

        $model1Versions = $model1->versions;
        self::assertCount(1, $model1Versions);

        $model1Version1 = $model1Versions[0];
        self::assertSame('svid1', $model1Version1->id);
        self::assertSame($model1, $model1Version1->software);
        self::assertSame('1', $model1Version1->majorVersion);
        self::assertSame('1.0.2', $model1Version1->version);
        self::assertSame('/foo/download/file', $model1Version1->downloadFile);
        self::assertSame(
            '2019-12-08T20:05:23+00:00',
            $model1Version1->releasedOn->format(
                DateTimeInterface::ATOM
            )
        );

        $model2 = $models[1];
        self::assertSame('id2', $model2->id);
        self::assertSame('bar-name', $model2->slug);
        self::assertSame('Bar Name', $model2->name);
        self::assertFalse($model2->isForSale);

        $model2Versions = $model2->versions;
        self::assertCount(2, $model2Versions);

        $model2Version1 = $model2Versions[0];
        self::assertSame('svid2', $model2Version1->id);
        self::assertSame($model2, $model2Version1->software);
        self::assertSame('3', $model2Version1->majorVersion);
        self::assertSame('3.4.6', $model2Version1->version);
        self::assertSame('', $model2Version1->downloadFile);
        self::assertSame(
            '2013-01-04T20:05:23+00:00',
            $model2Version1->releasedOn->format(
                DateTimeInterface::ATOM
            )
        );

        $model2Version2 = $model2Versions[1];
        self::assertSame('svid3', $model2Version2->id);
        self::assertSame($model2, $model2Version2->software);
        self::assertSame('10', $model2Version2->majorVersion);
        self::assertSame('10.1.8', $model2Version2->version);
        self::assertSame('', $model2Version2->downloadFile);
        self::assertSame(
            '1999-01-04T20:05:23+00:00',
            $model2Version2->releasedOn->format(
                DateTimeInterface::ATOM
            )
        );
    }

    protected function setUp() : void
    {
        $this->softwareRecordQueryAllReturn = [];
    }

    private function internalSetUp() : void
    {
        $this->softwareRecordQuery = $this->createMock(
            RecordQuery::class
        );

        $this->softwareRecordQuery->expects(self::at(0))
            ->method('withOrder')
            ->with(
                self::equalTo('name'),
                self::equalTo('asc')
            )
            ->willReturn($this->softwareRecordQuery);

        $this->softwareRecordQuery->expects(self::at(1))
            ->method('all')
            ->willReturn($this->softwareRecordQueryAllReturn);

        $this->recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $this->recordQueryFactory->expects(self::at(0))
            ->method('__invoke')
            ->willReturnCallback(
                function (Record $record) {
                    self::assertInstanceOf(
                        SoftwareRecord::class,
                        $record
                    );

                    return $this->softwareRecordQuery;
                }
            );

        $this->service = new FetchAllSoftware(
            $this->recordQueryFactory,
            TestConfig::$di->get(
                TransformSoftwareRecordToModel::class
            ),
            TestConfig::$di->get(
                TransformSoftwareVersionRecordToModel::class
            )
        );
    }

    /**
     * @param SoftwareVersionRecord[] $versionRecords
     *
     * @return RecordQuery&MockObject
     */
    private function mockVersionRecordQuery(array $versionRecords = []) : RecordQuery
    {
        $versionRecordQuery = $this->createMock(
            RecordQuery::class
        );

        $versionRecordQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('software_id'),
                self::equalTo(['id1', 'id2']),
                self::equalTo('IN')
            )
            ->willReturn($versionRecordQuery);

        $versionRecordQuery->expects(self::at(1))
            ->method('withOrder')
            ->with(
                self::equalTo('released_on'),
                self::equalTo('desc')
            )
            ->willReturn($versionRecordQuery);

        $versionRecordQuery->expects(self::at(2))
            ->method('withOrder')
            ->with(
                self::equalTo('major_version'),
                self::equalTo('desc')
            )
            ->willReturn($versionRecordQuery);

        $versionRecordQuery->expects(self::at(3))
            ->method('withOrder')
            ->with(
                self::equalTo('version'),
                self::equalTo('desc')
            )
            ->willReturn($versionRecordQuery);

        $versionRecordQuery->expects(self::at(4))
            ->method('all')
            ->willReturn($versionRecords);

        return $versionRecordQuery;
    }

    /**
     * @return SoftwareVersionRecord[]
     */
    private function mockVersionRecords() : array
    {
        $record1                = new SoftwareVersionRecord();
        $record1->id            = 'svid1';
        $record1->software_id   = 'id1';
        $record1->major_version = '1';
        $record1->version       = '1.0.2';
        $record1->download_file = '/foo/download/file';
        $record1->released_on   = '2019-12-08 20:05:23+00';

        $record2                = new SoftwareVersionRecord();
        $record2->id            = 'svid2';
        $record2->software_id   = 'id2';
        $record2->major_version = '3';
        $record2->version       = '3.4.6';
        $record2->download_file = '';
        $record2->released_on   = '2013-01-04 20:05:23+00';

        $record3                = new SoftwareVersionRecord();
        $record3->id            = 'svid3';
        $record3->software_id   = 'id2';
        $record3->major_version = '10';
        $record3->version       = '10.1.8';
        $record3->download_file = '';
        $record3->released_on   = '1999-01-04 20:05:23+00';

        return [$record1, $record2, $record3];
    }
}
