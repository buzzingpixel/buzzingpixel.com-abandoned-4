<?php

declare(strict_types=1);

namespace Tests\Software\Services;

use App\Persistence\Record;
use App\Persistence\RecordQuery;
use App\Persistence\RecordQueryFactory;
use App\Persistence\Software\SoftwareRecord;
use App\Persistence\Software\SoftwareVersionRecord;
use App\Software\Models\SoftwareVersionModel;
use App\Software\Services\FetchSoftwareBySlug;
use App\Software\Transformers\TransformSoftwareRecordToModel;
use App\Software\Transformers\TransformSoftwareVersionRecordToModel;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Tests\TestConfig;

class FetchSoftwareBySlugTest extends TestCase
{
    public function testWhenNoRecord() : void
    {
        $recordQuery = $this->createMock(RecordQuery::class);

        $recordQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('slug'),
                self::equalTo('foo-slug')
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
                        SoftwareRecord::class,
                        $record
                    );

                    return $recordQuery;
                }
            );

        $service = new FetchSoftwareBySlug(
            $recordQueryFactory,
            TestConfig::$di->get(
                TransformSoftwareRecordToModel::class
            ),
            TestConfig::$di->get(
                TransformSoftwareVersionRecordToModel::class
            ),
        );

        self::assertNull($service('foo-slug'));
    }

    public function testWhenNoVersions() : void
    {
        $softwareRecord              = new SoftwareRecord();
        $softwareRecord->id          = 'id1';
        $softwareRecord->slug        = 'foo-name';
        $softwareRecord->name        = 'Foo Name';
        $softwareRecord->is_for_sale = '0';

        $softwareQuery = $this->createMock(RecordQuery::class);

        $softwareQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('slug'),
                self::equalTo('foo-slug')
            )
            ->willReturn($softwareQuery);

        $softwareQuery->expects(self::at(1))
            ->method('one')
            ->willReturn($softwareRecord);

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $versionQuery = $this->createMock(RecordQuery::class);

        $versionQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('software_id'),
                self::equalTo('id1')
            )
            ->willReturn($versionQuery);

        $versionQuery->expects(self::at(1))
            ->method('withOrder')
            ->with(
                self::equalTo('released_on'),
                self::equalTo('desc')
            )
            ->willReturn($versionQuery);

        $versionQuery->expects(self::at(2))
            ->method('withOrder')
            ->with(
                self::equalTo('major_version'),
                self::equalTo('desc')
            )
            ->willReturn($versionQuery);

        $versionQuery->expects(self::at(3))
            ->method('withOrder')
            ->with(
                self::equalTo('version'),
                self::equalTo('desc')
            )
            ->willReturn($versionQuery);

        $versionQuery->expects(self::at(4))
            ->method('all')
            ->willReturn([]);

        $recordQueryFactory->expects(self::at(0))
            ->method('__invoke')
            ->willReturnCallback(
                static function (Record $record) use ($softwareQuery) {
                    self::assertInstanceOf(
                        SoftwareRecord::class,
                        $record
                    );

                    return $softwareQuery;
                }
            );

        $recordQueryFactory->expects(self::at(1))
            ->method('__invoke')
            ->willReturnCallback(
                static function (Record $record) use ($versionQuery) {
                    self::assertInstanceOf(
                        SoftwareVersionRecord::class,
                        $record
                    );

                    return $versionQuery;
                }
            );

        $service = new FetchSoftwareBySlug(
            $recordQueryFactory,
            TestConfig::$di->get(
                TransformSoftwareRecordToModel::class
            ),
            TestConfig::$di->get(
                TransformSoftwareVersionRecordToModel::class
            ),
        );

        $model = $service('foo-slug');
        self::assertNotNull($model);
        self::assertSame('id1', $model->getId());
        self::assertSame('foo-name', $model->getSlug());
        self::assertSame('Foo Name', $model->getName());
        self::assertFalse($model->isForSale());
        self::assertSame([], $model->getVersions());
    }

    public function testWhenWithVersions() : void
    {
        $softwareRecord              = new SoftwareRecord();
        $softwareRecord->id          = 'id1';
        $softwareRecord->slug        = 'foo-name';
        $softwareRecord->name        = 'Foo Name';
        $softwareRecord->is_for_sale = '0';

        $softwareQuery = $this->createMock(RecordQuery::class);

        $softwareQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('slug'),
                self::equalTo('foo-slug')
            )
            ->willReturn($softwareQuery);

        $softwareQuery->expects(self::at(1))
            ->method('one')
            ->willReturn($softwareRecord);

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $versionRecord1                = new SoftwareVersionRecord();
        $versionRecord1->id            = 'v1';
        $versionRecord1->software_id   = 'foo';
        $versionRecord1->major_version = '3';
        $versionRecord1->version       = '3.5.8';
        $versionRecord1->download_file = '/foo/file';
        $versionRecord1->released_on   = '2013-01-04 20:05:23+00';

        $versionRecord2                = new SoftwareVersionRecord();
        $versionRecord2->id            = 'v2';
        $versionRecord2->software_id   = 'id1';
        $versionRecord2->major_version = '9';
        $versionRecord2->version       = '9.5.0';
        $versionRecord2->download_file = '/foo/bar/file';
        $versionRecord2->released_on   = '1913-01-04 20:05:23+00';

        $versionQuery = $this->createMock(RecordQuery::class);

        $versionQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('software_id'),
                self::equalTo('id1')
            )
            ->willReturn($versionQuery);

        $versionQuery->expects(self::at(1))
            ->method('withOrder')
            ->with(
                self::equalTo('released_on'),
                self::equalTo('desc')
            )
            ->willReturn($versionQuery);

        $versionQuery->expects(self::at(2))
            ->method('withOrder')
            ->with(
                self::equalTo('major_version'),
                self::equalTo('desc')
            )
            ->willReturn($versionQuery);

        $versionQuery->expects(self::at(3))
            ->method('withOrder')
            ->with(
                self::equalTo('version'),
                self::equalTo('desc')
            )
            ->willReturn($versionQuery);

        $versionQuery->expects(self::at(4))
            ->method('all')
            ->willReturn([$versionRecord1, $versionRecord2]);

        $recordQueryFactory->expects(self::at(0))
            ->method('__invoke')
            ->willReturnCallback(
                static function (Record $record) use ($softwareQuery) {
                    self::assertInstanceOf(
                        SoftwareRecord::class,
                        $record
                    );

                    return $softwareQuery;
                }
            );

        $recordQueryFactory->expects(self::at(1))
            ->method('__invoke')
            ->willReturnCallback(
                static function (Record $record) use ($versionQuery) {
                    self::assertInstanceOf(
                        SoftwareVersionRecord::class,
                        $record
                    );

                    return $versionQuery;
                }
            );

        $service = new FetchSoftwareBySlug(
            $recordQueryFactory,
            TestConfig::$di->get(
                TransformSoftwareRecordToModel::class
            ),
            TestConfig::$di->get(
                TransformSoftwareVersionRecordToModel::class
            ),
        );

        $model = $service('foo-slug');
        self::assertNotNull($model);
        self::assertSame('id1', $model->getId());
        self::assertSame('foo-name', $model->getSlug());
        self::assertSame('Foo Name', $model->getName());
        self::assertFalse($model->isForSale());

        $versions = $model->getVersions();

        self::assertCount(2, $versions);

        /** @var SoftwareVersionModel|null $versionModel1 */
        $versionModel1 = $versions[0];
        self::assertInstanceOf(
            SoftwareVersionModel::class,
            $versionModel1
        );
        self::assertSame('v1', $versionModel1->getId());
        self::assertSame(
            $model,
            $versionModel1->getSoftware()
        );
        self::assertSame(
            '3',
            $versionModel1->getMajorVersion()
        );
        self::assertSame(
            '3.5.8',
            $versionModel1->getVersion()
        );
        self::assertSame(
            '/foo/file',
            $versionModel1->getDownloadFile()
        );
        $releasedOn1 = $versionModel1->getReleasedOn();
        self::assertInstanceOf(
            DateTimeImmutable::class,
            $releasedOn1
        );
        self::assertSame(
            '2013-01-04T20:05:23+00:00',
            $releasedOn1->format(DateTimeInterface::ATOM)
        );

        /** @var SoftwareVersionModel|null $versionModel2 */
        $versionModel2 = $versions[1];
        self::assertInstanceOf(
            SoftwareVersionModel::class,
            $versionModel2
        );
        self::assertSame('v2', $versionModel2->getId());
        self::assertSame(
            $model,
            $versionModel2->getSoftware()
        );
        self::assertSame(
            '9',
            $versionModel2->getMajorVersion()
        );
        self::assertSame(
            '9.5.0',
            $versionModel2->getVersion()
        );
        self::assertSame(
            '/foo/bar/file',
            $versionModel2->getDownloadFile()
        );
        $releasedOn2 = $versionModel2->getReleasedOn();
        self::assertInstanceOf(
            DateTimeImmutable::class,
            $releasedOn2
        );
        self::assertSame(
            '1913-01-04T20:05:23+00:00',
            $releasedOn2->format(DateTimeInterface::ATOM)
        );
    }
}
