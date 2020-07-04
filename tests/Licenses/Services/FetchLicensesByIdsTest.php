<?php

declare(strict_types=1);

namespace Tests\Licenses\Services;

use _HumbugBox89320708a2e3\Nette\Neon\Exception;
use App\Licenses\Models\LicenseModel;
use App\Licenses\Services\FetchLicensesByIds;
use App\Licenses\Transformers\TransformLicenseRecordToModel;
use App\Persistence\Licenses\LicenseRecord;
use App\Persistence\RecordQuery;
use App\Persistence\RecordQueryFactory;
use PHPUnit\Framework\TestCase;

class FetchLicensesByIdsTest extends TestCase
{
    public function testWhenThrows(): void
    {
        $record     = new LicenseRecord();
        $record->id = 'foo-id';

        $recordQuery = $this->createMock(RecordQuery::class);

        $recordQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('id'),
                self::equalTo(['foo-test-id']),
            )
            ->willThrowException(new Exception());

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static fn (LicenseRecord $record) => $recordQuery
            );

        $model = new LicenseModel();

        $transformer = $this->createMock(
            TransformLicenseRecordToModel::class
        );

        $service = new FetchLicensesByIds(
            $recordQueryFactory,
            $transformer
        );

        self::assertSame(
            [],
            $service(['foo-test-id'])
        );
    }

    public function testWhenNoIds(): void
    {
        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->expects(self::never())
            ->method(self::anything());

        $transformer = $this->createMock(
            TransformLicenseRecordToModel::class
        );

        $service = new FetchLicensesByIds(
            $recordQueryFactory,
            $transformer
        );

        self::assertSame(
            [],
            $service([])
        );
    }

    public function test(): void
    {
        $record     = new LicenseRecord();
        $record->id = 'foo-id';

        $recordQuery = $this->createMock(RecordQuery::class);

        $recordQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('id'),
                self::equalTo(['foo-test-id']),
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(1))
            ->method('withOrder')
            ->with(
                self::equalTo('item_key'),
                self::equalTo('asc'),
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(2))
            ->method('withOrder')
            ->with(
                self::equalTo('major_version'),
                self::equalTo('desc'),
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(3))
            ->method('withOrder')
            ->with(
                self::equalTo('version'),
                self::equalTo('desc'),
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(4))
            ->method('withOrder')
            ->with(
                self::equalTo('id'),
                self::equalTo('desc'),
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(5))
            ->method('all')
            ->willReturn([$record]);

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static fn (LicenseRecord $record) => $recordQuery
            );

        $model = new LicenseModel();

        $transformer = $this->createMock(
            TransformLicenseRecordToModel::class
        );

        $transformer->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($record))
            ->willReturn($model);

        $service = new FetchLicensesByIds(
            $recordQueryFactory,
            $transformer
        );

        self::assertSame(
            [$model],
            $service(['foo-test-id'])
        );
    }
}
