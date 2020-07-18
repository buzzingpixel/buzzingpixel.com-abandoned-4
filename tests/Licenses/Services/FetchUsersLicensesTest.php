<?php

declare(strict_types=1);

namespace Tests\Licenses\Services;

use App\Licenses\Models\LicenseModel;
use App\Licenses\Services\FetchUsersLicenses;
use App\Licenses\Transformers\TransformLicenseRecordToModel;
use App\Persistence\Licenses\LicenseRecord;
use App\Persistence\RecordQuery;
use App\Persistence\RecordQueryFactory;
use App\Users\Models\UserModel;
use PHPUnit\Framework\TestCase;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class FetchUsersLicensesTest extends TestCase
{
    public function test(): void
    {
        $user     = new UserModel();
        $user->id = 'foo-user-id';

        $record     = new LicenseRecord();
        $record->id = 'foo-id';

        $recordQuery = $this->createMock(RecordQuery::class);

        $recordQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('owner_user_id'),
                self::equalTo('foo-user-id')
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(1))
            ->method('withOrder')
            ->with(
                self::equalTo('item_key'),
                self::equalTo('asc')
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(2))
            ->method('withOrder')
            ->with(
                self::equalTo('major_version'),
                self::equalTo('desc')
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(3))
            ->method('withOrder')
            ->with(
                self::equalTo('version'),
                self::equalTo('desc')
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(4))
            ->method('withOrder')
            ->with(
                self::equalTo('id'),
                self::equalTo('desc')
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
            ->with(
                self::equalTo($record),
                self::equalTo($user),
            )
            ->willReturn($model);

        $service = new FetchUsersLicenses(
            $recordQueryFactory,
            $transformer
        );

        self::assertSame(
            [$model],
            $service($user)
        );
    }
}
