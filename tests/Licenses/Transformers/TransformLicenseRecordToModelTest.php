<?php

declare(strict_types=1);

namespace Tests\Licenses\Transformers;

use App\Licenses\Models\LicenseModel;
use App\Licenses\Transformers\TransformLicenseRecordToModel;
use App\Persistence\Constants;
use App\Persistence\Licenses\LicenseRecord;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Throwable;

use function assert;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformLicenseRecordToModelTest extends TestCase
{
    private DateTimeImmutable $expires;

    public function setUp(): void
    {
        $this->expires = new DateTimeImmutable();
    }

    /**
     * @throws Throwable
     */
    public function testWithUserModel(): void
    {
        $record = $this->createRecord();

        $user     = new UserModel();
        $user->id = 'foo-owner-id';

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::never())
            ->method(self::anything());

        $transformer = new TransformLicenseRecordToModel($userApi);

        $this->validateModel(
            $transformer($record, $user),
            $user,
        );
    }

    /**
     * @throws Throwable
     */
    public function testWhenUserModelIdDoesntMatch(): void
    {
        $record = $this->createRecord();

        $user     = new UserModel();
        $user->id = 'asdf';

        $altUser  = new UserModel();
        $user->id = 'foo-test';

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchUserById')
            ->with(self::equalTo(
                $record->owner_user_id
            ))
            ->willReturn($altUser);

        $transformer = new TransformLicenseRecordToModel($userApi);

        $this->validateModel(
            $transformer($record, $user),
            $altUser,
        );
    }

    /**
     * @throws Throwable
     */
    public function testWithNoUserModelArg(): void
    {
        $record = $this->createRecord();

        $user     = new UserModel();
        $user->id = 'bar';

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchUserById')
            ->with(self::equalTo(
                $record->owner_user_id
            ))
            ->willReturn($user);

        $transformer = new TransformLicenseRecordToModel($userApi);

        $this->validateModel(
            $transformer($record),
            $user,
        );
    }

    private function createRecord(): LicenseRecord
    {
        $record                         = new LicenseRecord();
        $record->id                     = 'foo-id';
        $record->owner_user_id          = 'foo-owner-id';
        $record->item_key               = 'foo-key';
        $record->item_title             = 'foo-title';
        $record->major_version          = 'foo-major-version';
        $record->version                = 'foo-version';
        $record->last_available_version = 'foo-last-avail-version';
        $record->notes                  = 'foo-notes';
        $record->authorized_domains     = '["foo","bar"]';
        $record->is_disabled            = '0';
        $record->expires                = $this->expires->format(
            Constants::POSTGRES_OUTPUT_FORMAT
        );

        return $record;
    }

    private function validateModel(LicenseModel $model, UserModel $user): void
    {
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
            $this->expires->format(DateTimeImmutable::ATOM),
            $modelExpires->format(DateTimeImmutable::ATOM),
        );
    }
}
