<?php

declare(strict_types=1);

namespace Tests\Licenses\Services;

use App\Licenses\Services\FetchUsersLicenses;
use App\Licenses\Transformers\TransformLicenseRecordToModel;
use App\Persistence\Constants;
use App\Persistence\Licenses\LicenseRecord;
use App\Persistence\RecordQuery;
use App\Persistence\RecordQueryFactory;
use App\Users\Models\UserModel;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Tests\TestConfig;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class FetchUsersLicensesTest extends TestCase
{
    public function test() : void
    {
        $expires = new DateTimeImmutable();

        $user     = new UserModel();
        $user->id = 'foo-user-id';

        $model                         = new LicenseRecord();
        $model->id                     = 'foo-id';
        $model->owner_user_id          = 'asdf';
        $model->item_key               = 'foo-key';
        $model->item_title             = 'foo-title';
        $model->major_version          = 'foo-major-version';
        $model->version                = 'foo-version';
        $model->last_available_version = 'foo-last-avail-version';
        $model->notes                  = 'foo-notes';
        $model->authorized_domains     = '["foo","bar"]';
        $model->is_disabled            = '0';
        $model->expires                = $expires->format(
            Constants::POSTGRES_OUTPUT_FORMAT
        );

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
            ->willReturn([$model]);

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static fn(LicenseRecord $record) => $recordQuery
            );

        $service = new FetchUsersLicenses(
            $recordQueryFactory,
            TestConfig::$di->get(
                TransformLicenseRecordToModel::class
            )
        );

        $models = $service($user);

        self::assertCount(1, $models);

        $model = $models[0];

        self::assertSame(
            'foo-id',
            $model->id,
        );

        self::assertSame(
            $user,
            $model->ownerUser
        );

        self::assertSame(
            'foo-key',
            $model->itemKey,
        );

        self::assertSame(
            'foo-title',
            $model->itemTitle,
        );

        self::assertSame(
            'foo-major-version',
            $model->majorVersion,
        );

        self::assertSame(
            'foo-version',
            $model->version,
        );

        self::assertSame(
            'foo-last-avail-version',
            $model->lastAvailableVersion,
        );

        self::assertSame(
            'foo-notes',
            $model->notes,
        );

        self::assertSame(
            [
                0 => 'foo',
                1 => 'bar',
            ],
            $model->authorizedDomains,
        );

        self::assertFalse($model->isDisabled);

        self::assertSame(
            $expires->format(DateTimeImmutable::ATOM),
            $model->expires->format(DateTimeImmutable::ATOM),
        );
    }
}
