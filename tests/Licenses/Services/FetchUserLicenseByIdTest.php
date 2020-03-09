<?php

declare(strict_types=1);

namespace Tests\Licenses\Services;

use App\Licenses\Models\LicenseModel;
use App\Licenses\Services\FetchUserLicenseById;
use App\Licenses\Transformers\TransformLicenseRecordToModel;
use App\Persistence\Constants;
use App\Persistence\Licenses\LicenseRecord;
use App\Persistence\RecordQuery;
use App\Persistence\RecordQueryFactory;
use App\Users\Models\UserModel;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;
use Tests\TestConfig;
use function assert;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class FetchUserLicenseByIdTest extends TestCase
{
    public function testWhenExceptionThrown() : void
    {
        $user = new UserModel();

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->method(self::anything())
            ->willThrowException(new Exception());

        $service = new FetchUserLicenseById(
            $recordQueryFactory,
            TestConfig::$di->get(
                TransformLicenseRecordToModel::class
            ),
        );

        self::assertNull($service($user, 'foo-id'));
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
                self::equalTo('owner_user_id'),
                self::equalTo('foo-user-id')
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(1))
            ->method('withWhere')
            ->with(
                self::equalTo('id'),
                self::equalTo('foo-id')
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(2))
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

        $service = new FetchUserLicenseById(
            $recordQueryFactory,
            TestConfig::$di->get(
                TransformLicenseRecordToModel::class
            ),
        );

        self::assertNull($service($user, 'foo-id'));
    }

    public function test() : void
    {
        $expires = new DateTimeImmutable();

        $user     = new UserModel();
        $user->id = 'foo-user-id';

        $record                         = new LicenseRecord();
        $record->id                     = 'foo-id';
        $record->owner_user_id          = 'asdf';
        $record->item_key               = 'foo-key';
        $record->item_title             = 'foo-title';
        $record->major_version          = 'foo-major-version';
        $record->version                = 'foo-version';
        $record->last_available_version = 'foo-last-avail-version';
        $record->notes                  = 'foo-notes';
        $record->authorized_domains     = '["foo","bar"]';
        $record->is_disabled            = '0';
        $record->expires                = $expires->format(
            Constants::POSTGRES_OUTPUT_FORMAT
        );

        $recordQuery = $this->createMock(
            RecordQuery::class
        );

        $recordQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('owner_user_id'),
                self::equalTo('foo-user-id')
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(1))
            ->method('withWhere')
            ->with(
                self::equalTo('id'),
                self::equalTo('foo-id')
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(2))
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

        $service = new FetchUserLicenseById(
            $recordQueryFactory,
            TestConfig::$di->get(
                TransformLicenseRecordToModel::class
            ),
        );

        $model = $service($user, 'foo-id');

        assert($model instanceof LicenseModel);

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

        $modelExpires = $model->expires;

        assert($modelExpires instanceof DateTimeImmutable);

        self::assertSame(
            $expires->format(DateTimeImmutable::ATOM),
            $modelExpires->format(DateTimeImmutable::ATOM),
        );
    }
}
