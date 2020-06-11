<?php

declare(strict_types=1);

namespace Tests\Users\Services;

use App\Payload\Payload;
use App\Persistence\SaveExistingRecord;
use App\Persistence\SaveNewRecord;
use App\Persistence\UserCards\UserCardRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use App\Users\Services\SaveUserCard;
use App\Users\Transformers\TransformUserCardModelToRecord;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use PHPUnit\Framework\TestCase;
use Tests\TestConfig;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class SaveUserCardTest extends TestCase
{
    public function testWhenExceptionThrown() : void
    {
        $saveNewRecord = $this->createMock(
            SaveNewRecord::class
        );

        $saveNewRecord->expects(self::once())
            ->method('__invoke')
            ->willThrowException(
                new Exception()
            );

        $saveExistingRecord = $this->createMock(
            SaveExistingRecord::class
        );

        $saveExistingRecord->expects(self::never())
            ->method(self::anything());

        $service = new SaveUserCard(
            $saveNewRecord,
            $saveExistingRecord,
            TestConfig::$di->get(
                UuidFactoryWithOrderedTimeCodec::class
            ),
            TestConfig::$di->get(
                TransformUserCardModelToRecord::class
            ),
        );

        $model = new UserCardModel();

        $model->user = new UserModel();

        $model->stripeId = 'asdf-stripe';

        $model->lastFour = 'asdf';

        $model->provider = 'asdf';

        $model->expiration = new DateTimeImmutable();

        $payload = $service($model);

        self::assertSame(
            Payload::STATUS_ERROR,
            $payload->getStatus(),
        );

        self::assertSame(
            ['message' => 'An unknown error occurred'],
            $payload->getResult(),
        );
    }

    public function testSaveNew() : void
    {
        $expiration = new DateTimeImmutable();

        $user = new UserModel();

        $user->id = 'foo-bar-id';

        $returnPayload = new Payload(Payload::STATUS_CREATED);

        $saveNewRecord = $this->createMock(
            SaveNewRecord::class
        );

        $saveNewRecord->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (UserCardRecord $record) use (
                    $returnPayload,
                    $expiration,
                    $user
                ) : Payload {
                    self::assertNotEmpty($record->id);

                    self::assertSame(
                        $user->id,
                        $record->user_id,
                    );

                    self::assertSame(
                        'foo-stripe-id',
                        $record->stripe_id,
                    );

                    self::assertSame(
                        'foo-nickname',
                        $record->nickname,
                    );

                    self::assertSame(
                        '4321',
                        $record->last_four,
                    );

                    self::assertSame(
                        'visa',
                        $record->provider,
                    );

                    self::assertSame(
                        '1',
                        $record->is_default,
                    );

                    self::assertSame(
                        $expiration->format(DateTimeInterface::ATOM),
                        $record->expiration,
                    );

                    return $returnPayload;
                }
            );

        $saveExistingRecord = $this->createMock(
            SaveExistingRecord::class
        );

        $saveExistingRecord->expects(self::never())
            ->method(self::anything());

        $service = new SaveUserCard(
            $saveNewRecord,
            $saveExistingRecord,
            TestConfig::$di->get(
                UuidFactoryWithOrderedTimeCodec::class
            ),
            TestConfig::$di->get(
                TransformUserCardModelToRecord::class
            ),
        );

        $model = new UserCardModel();

        $model->user = $user;

        $model->stripeId = 'foo-stripe-id';

        $model->nickname = 'foo-nickname';

        $model->lastFour = '4321';

        $model->provider = 'visa';

        $model->expiration = $expiration;

        $model->isDefault = true;

        self::assertSame(
            $returnPayload,
            $service($model),
        );
    }

    public function testSaveExistingRecord() : void
    {
        $expiration = new DateTimeImmutable();

        $user = new UserModel();

        $user->id = 'bar-id';

        $returnPayload = new Payload(Payload::STATUS_CREATED);

        $saveNewRecord = $this->createMock(
            SaveNewRecord::class
        );

        $saveNewRecord->expects(self::never())
            ->method(self::anything());

        $saveExisting = $this->createMock(
            SaveExistingRecord::class
        );

        $saveExisting->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (UserCardRecord $record) use (
                    $returnPayload,
                    $expiration,
                    $user
                ) : Payload {
                    self::assertSame(
                        'foo-record-id',
                        $record->id,
                    );

                    self::assertSame(
                        $user->id,
                        $record->user_id,
                    );

                    self::assertSame(
                        'foo-stripe-id',
                        $record->stripe_id,
                    );

                    self::assertSame(
                        '',
                        $record->nickname,
                    );

                    self::assertSame(
                        '4321',
                        $record->last_four,
                    );

                    self::assertSame(
                        'visa',
                        $record->provider,
                    );

                    self::assertSame(
                        '0',
                        $record->is_default,
                    );

                    self::assertSame(
                        $expiration->format(DateTimeInterface::ATOM),
                        $record->expiration,
                    );

                    return $returnPayload;
                }
            );

        $service = new SaveUserCard(
            $saveNewRecord,
            $saveExisting,
            TestConfig::$di->get(
                UuidFactoryWithOrderedTimeCodec::class
            ),
            TestConfig::$di->get(
                TransformUserCardModelToRecord::class
            ),
        );

        $model = new UserCardModel();

        $model->id = 'foo-record-id';

        $model->user = $user;

        $model->stripeId = 'foo-stripe-id';

        $model->lastFour = '4321';

        $model->provider = 'visa';

        $model->expiration = $expiration;

        self::assertSame(
            $returnPayload,
            $service($model),
        );
    }
}
