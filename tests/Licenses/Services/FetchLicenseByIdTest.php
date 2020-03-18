<?php

declare(strict_types=1);

namespace Tests\Licenses\Services;

use App\Licenses\Models\LicenseModel;
use App\Licenses\Services\FetchLicenseById;
use App\Licenses\Transformers\TransformLicenseRecordToModel;
use App\Persistence\Licenses\LicenseRecord;
use App\Persistence\RecordQuery;
use App\Persistence\RecordQueryFactory;
use App\Users\Models\UserModel;
use Exception;
use PHPUnit\Framework\TestCase;

class FetchLicenseByIdTest extends TestCase
{
    public function testWhenExceptionThrown() : void
    {
        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->method(self::anything())
            ->willThrowException(new Exception());

        $transformer = $this->createMock(
            TransformLicenseRecordToModel::class
        );

        $transformer->expects(self::never())
            ->method(self::anything());

        $service = new FetchLicenseById(
            $recordQueryFactory,
            $transformer,
        );

        self::assertNull($service('foo-id'));
    }

    public function testWhenNoLicense() : void
    {
        $user     = new UserModel();
        $user->id = 'foo-user-id';

        $recordQuery = $this->createMock(
            RecordQuery::class
        );

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
                static fn(LicenseRecord $record) => $recordQuery
            );

        $transformer = $this->createMock(
            TransformLicenseRecordToModel::class
        );

        $transformer->expects(self::never())
            ->method(self::anything());

        $service = new FetchLicenseById(
            $recordQueryFactory,
            $transformer,
        );

        self::assertNull($service('foo-id', $user));
    }

    public function test() : void
    {
        $user     = new UserModel();
        $user->id = 'foo-user-id';

        $record     = new LicenseRecord();
        $record->id = 'foo-id';

        $recordQuery = $this->createMock(
            RecordQuery::class
        );

        $recordQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('id'),
                self::equalTo('foo-id')
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(1))
            ->method('one')
            ->willReturn($record);

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static fn(LicenseRecord $record) => $recordQuery
            );

        $model = new LicenseModel();

        $transformer = $this->createMock(
            TransformLicenseRecordToModel::class
        );

        $transformer->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo($record),
                self::equalTo($user),
            )
            ->willReturn($model);

        $service = new FetchLicenseById(
            $recordQueryFactory,
            $transformer,
        );

        self::assertSame(
            $model,
            $service('foo-id', $user)
        );
    }
}
