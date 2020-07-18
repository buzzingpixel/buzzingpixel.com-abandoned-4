<?php

declare(strict_types=1);

namespace Tests\Subscriptions\Services;

use _HumbugBox89320708a2e3\Nette\Neon\Exception;
use App\Licenses\Models\LicenseModel;
use App\Orders\Models\OrderModel;
use App\Payload\Payload;
use App\Persistence\DatabaseTransactionManager;
use App\Persistence\SaveExistingRecord;
use App\Persistence\SaveNewRecord;
use App\Persistence\Subscriptions\SubscriptionRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use App\Subscriptions\Events\SaveSubscriptionAfterSave;
use App\Subscriptions\Events\SaveSubscriptionBeforeSave;
use App\Subscriptions\Models\SubscriptionModel;
use App\Subscriptions\Services\SaveSubscription;
use App\Subscriptions\Transformers\TransformSubscriptionModelToRecord;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Tests\TestConfig;
use Throwable;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class SaveSubscriptionTest extends TestCase
{
    public function testWhenThrows(): void
    {
        $transactionManager = $this->createMock(
            DatabaseTransactionManager::class,
        );

        $transactionManager->expects(self::once())
            ->method('beginTransaction')
            ->willThrowException(new Exception());

        $transactionManager->expects(self::once())
            ->method('rollBack');

        $transactionManager->expects(self::never())
            ->method('commit');

        $modelToRecord = $this->createMock(
            TransformSubscriptionModelToRecord::class,
        );

        $modelToRecord->expects(self::never())
            ->method(self::anything());

        $saveNewRecord = $this->createMock(
            SaveNewRecord::class,
        );

        $saveNewRecord->expects(self::never())
            ->method(self::anything());

        $saveExistingRecord = $this->createMock(
            SaveExistingRecord::class,
        );

        $saveExistingRecord->expects(self::never())
            ->method(self::anything());

        $uuidFactory = $this->createMock(
            UuidFactoryWithOrderedTimeCodec::class,
        );

        $uuidFactory->expects(self::never())
            ->method(self::anything());

        $eventDispatcher = $this->createMock(
            EventDispatcherInterface::class,
        );

        $eventDispatcher->expects(self::never())
            ->method(self::anything());

        $service = new SaveSubscription(
            $transactionManager,
            $modelToRecord,
            $saveNewRecord,
            $saveExistingRecord,
            $uuidFactory,
            $eventDispatcher,
        );

        $model = new SubscriptionModel();

        $payload = $service($model);

        self::assertSame(
            Payload::STATUS_ERROR,
            $payload->getStatus(),
        );

        self::assertSame(
            [],
            $payload->getResult(),
        );
    }

    public function testWhenErrors(): void
    {
        $model = new SubscriptionModel();

        $transactionManager = $this->createMock(
            DatabaseTransactionManager::class,
        );

        $transactionManager->expects(self::once())
            ->method('beginTransaction');

        $transactionManager->expects(self::once())
            ->method('rollBack');

        $transactionManager->expects(self::never())
            ->method('commit');

        $modelToRecord = $this->createMock(
            TransformSubscriptionModelToRecord::class,
        );

        $modelToRecord->expects(self::never())
            ->method(self::anything());

        $saveNewRecord = $this->createMock(
            SaveNewRecord::class,
        );

        $saveNewRecord->expects(self::never())
            ->method(self::anything());

        $saveExistingRecord = $this->createMock(
            SaveExistingRecord::class,
        );

        $saveExistingRecord->expects(self::never())
            ->method(self::anything());

        $uuidFactory = $this->createMock(
            UuidFactoryWithOrderedTimeCodec::class,
        );

        $uuidFactory->expects(self::never())
            ->method(self::anything());

        $eventDispatcher = $this->createMock(
            EventDispatcherInterface::class,
        );

        $eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->willReturnCallback(
                static function (
                    SaveSubscriptionBeforeSave $event
                ) use ($model): void {
                    self::assertSame(
                        $model,
                        $event->subscriptionModel,
                    );

                    $event->errors[] = 'foo test error';
                }
            );

        $service = new SaveSubscription(
            $transactionManager,
            $modelToRecord,
            $saveNewRecord,
            $saveExistingRecord,
            $uuidFactory,
            $eventDispatcher,
        );

        $payload = $service($model);

        self::assertSame(
            Payload::STATUS_ERROR,
            $payload->getStatus(),
        );

        self::assertSame(
            [
                'errors' => [
                    'User must be set',
                    'License must be set',
                    'foo test error',
                ],
            ],
            $payload->getResult(),
        );
    }

    public function testWhenErrorsOnlyFromEvent(): void
    {
        $model = new SubscriptionModel();

        $transactionManager = $this->createMock(
            DatabaseTransactionManager::class,
        );

        $transactionManager->expects(self::once())
            ->method('beginTransaction');

        $transactionManager->expects(self::once())
            ->method('rollBack');

        $modelToRecord = $this->createMock(
            TransformSubscriptionModelToRecord::class,
        );

        $modelToRecord->expects(self::never())
            ->method(self::anything());

        $saveNewRecord = $this->createMock(
            SaveNewRecord::class,
        );

        $saveNewRecord->expects(self::never())
            ->method(self::anything());

        $saveExistingRecord = $this->createMock(
            SaveExistingRecord::class,
        );

        $saveExistingRecord->expects(self::never())
            ->method(self::anything());

        $uuidFactory = $this->createMock(
            UuidFactoryWithOrderedTimeCodec::class,
        );

        $uuidFactory->expects(self::never())
            ->method(self::anything());

        $eventDispatcher = $this->createMock(
            EventDispatcherInterface::class,
        );

        $eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->willReturnCallback(
                static function (
                    SaveSubscriptionBeforeSave $event
                ) use ($model): void {
                    self::assertSame(
                        $model,
                        $event->subscriptionModel,
                    );

                    $event->errors[] = 'foo test error';
                }
            );

        $service = new SaveSubscription(
            $transactionManager,
            $modelToRecord,
            $saveNewRecord,
            $saveExistingRecord,
            $uuidFactory,
            $eventDispatcher,
        );

        $user = new UserModel();

        $license = new LicenseModel();

        $model->user = $user;

        $model->license = $license;

        $payload = $service($model);

        self::assertSame(
            Payload::STATUS_ERROR,
            $payload->getStatus(),
        );

        self::assertSame(
            [
                'errors' => ['foo test error'],
            ],
            $payload->getResult(),
        );
    }

    public function testWhenEventSetsInvalid(): void
    {
        $model = new SubscriptionModel();

        $transactionManager = $this->createMock(
            DatabaseTransactionManager::class,
        );

        $transactionManager->expects(self::once())
            ->method('beginTransaction');

        $transactionManager->expects(self::once())
            ->method('rollBack');

        $modelToRecord = $this->createMock(
            TransformSubscriptionModelToRecord::class,
        );

        $modelToRecord->expects(self::never())
            ->method(self::anything());

        $saveNewRecord = $this->createMock(
            SaveNewRecord::class,
        );

        $saveNewRecord->expects(self::never())
            ->method(self::anything());

        $saveExistingRecord = $this->createMock(
            SaveExistingRecord::class,
        );

        $saveExistingRecord->expects(self::never())
            ->method(self::anything());

        $uuidFactory = $this->createMock(
            UuidFactoryWithOrderedTimeCodec::class,
        );

        $uuidFactory->expects(self::never())
            ->method(self::anything());

        $eventDispatcher = $this->createMock(
            EventDispatcherInterface::class,
        );

        $eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->willReturnCallback(
                static function (
                    SaveSubscriptionBeforeSave $event
                ) use ($model): void {
                    self::assertSame(
                        $model,
                        $event->subscriptionModel,
                    );

                    $event->isValid = false;
                }
            );

        $service = new SaveSubscription(
            $transactionManager,
            $modelToRecord,
            $saveNewRecord,
            $saveExistingRecord,
            $uuidFactory,
            $eventDispatcher,
        );

        $user = new UserModel();

        $license = new LicenseModel();

        $model->user = $user;

        $model->license = $license;

        $payload = $service($model);

        self::assertSame(
            Payload::STATUS_ERROR,
            $payload->getStatus(),
        );

        self::assertSame(
            ['errors' => []],
            $payload->getResult(),
        );
    }

    /**
     * @throws Throwable
     */
    public function testNew(): void
    {
        $uuid = TestConfig::$di->get(
            UuidFactoryWithOrderedTimeCodec::class
        )->uuid1();

        $model = new SubscriptionModel();

        $payload = new Payload(Payload::STATUS_CREATED);

        $transactionManager = $this->createMock(
            DatabaseTransactionManager::class,
        );

        $transactionManager->expects(self::once())
            ->method('beginTransaction');

        $transactionManager->expects(self::never())
            ->method('rollBack');

        $transactionManager->expects(self::once())
            ->method('commit');

        $saveNewRecord = $this->createMock(
            SaveNewRecord::class,
        );

        $saveNewRecord->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    SubscriptionRecord $record
                ) use (
                    $payload,
                    $uuid
                ): Payload {
                    self::assertSame(
                        $uuid->toString(),
                        $record->id,
                    );

                    self::assertSame(
                        'foo-user-id',
                        $record->user_id,
                    );

                    self::assertSame(
                        'foo-license-id',
                        $record->license_id,
                    );

                    self::assertSame(
                        '[]',
                        $record->order_ids
                    );

                    self::assertSame(
                        '1',
                        $record->auto_renew,
                    );

                    self::assertNull($record->card_id);

                    return $payload;
                }
            );

        $saveExistingRecord = $this->createMock(
            SaveExistingRecord::class,
        );

        $saveExistingRecord->expects(self::never())
            ->method(self::anything());

        $uuidFactory = $this->createMock(
            UuidFactoryWithOrderedTimeCodec::class,
        );

        $uuidFactory->expects(self::once())
            ->method('uuid1')
            ->willReturn($uuid);

        $eventDispatcher = $this->createMock(
            EventDispatcherInterface::class,
        );

        $eventDispatcher->expects(self::at(0))
            ->method('dispatch')
            ->willReturnCallback(
                static function (
                    SaveSubscriptionBeforeSave $event
                ) use ($model): void {
                    self::assertSame(
                        $model,
                        $event->subscriptionModel,
                    );
                }
            );

        $eventDispatcher->expects(self::at(1))
            ->method('dispatch')
            ->willReturnCallback(
                static function (
                    SaveSubscriptionAfterSave $event
                ) use (
                    $model,
                    $payload
                ): void {
                    self::assertSame(
                        $model,
                        $event->subscriptionModel,
                    );

                    self::assertSame(
                        $payload,
                        $event->savePayload,
                    );
                }
            );

        $service = new SaveSubscription(
            $transactionManager,
            TestConfig::$di->get(
                TransformSubscriptionModelToRecord::class,
            ),
            $saveNewRecord,
            $saveExistingRecord,
            $uuidFactory,
            $eventDispatcher,
        );

        $user     = new UserModel();
        $user->id = 'foo-user-id';

        $license     = new LicenseModel();
        $license->id = 'foo-license-id';

        $model->user = $user;

        $model->license = $license;

        self::assertSame(
            $payload,
            $service($model),
        );
    }

    /**
     * @throws Throwable
     */
    public function testExisting(): void
    {
        $model     = new SubscriptionModel();
        $model->id = 'foo-test-id';

        $payload = new Payload(Payload::STATUS_UPDATED);

        $transactionManager = $this->createMock(
            DatabaseTransactionManager::class,
        );

        $transactionManager->expects(self::once())
            ->method('beginTransaction');

        $transactionManager->expects(self::never())
            ->method('rollBack');

        $transactionManager->expects(self::once())
            ->method('commit');

        $saveNewRecord = $this->createMock(
            SaveNewRecord::class,
        );

        $saveNewRecord->expects(self::never())
            ->method(self::anything());

        $saveExistingRecord = $this->createMock(
            SaveExistingRecord::class,
        );

        $saveExistingRecord->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    SubscriptionRecord $record
                ) use (
                    $payload
                ): Payload {
                    self::assertSame(
                        'foo-test-id',
                        $record->id,
                    );

                    self::assertSame(
                        'foo-user-id',
                        $record->user_id,
                    );

                    self::assertSame(
                        'foo-license-id',
                        $record->license_id,
                    );

                    self::assertSame(
                        '["foo-order-1-id","foo-order-2-id"]',
                        $record->order_ids
                    );

                    self::assertSame(
                        '0',
                        $record->auto_renew,
                    );

                    self::assertSame(
                        'foo-card-id',
                        $record->card_id,
                    );

                    return $payload;
                }
            );

        $uuidFactory = $this->createMock(
            UuidFactoryWithOrderedTimeCodec::class,
        );

        $uuidFactory->expects(self::never())
            ->method(self::anything());

        $eventDispatcher = $this->createMock(
            EventDispatcherInterface::class,
        );

        $eventDispatcher->expects(self::at(0))
            ->method('dispatch')
            ->willReturnCallback(
                static function (
                    SaveSubscriptionBeforeSave $event
                ) use ($model): void {
                    self::assertSame(
                        $model,
                        $event->subscriptionModel,
                    );
                }
            );

        $eventDispatcher->expects(self::at(1))
            ->method('dispatch')
            ->willReturnCallback(
                static function (
                    SaveSubscriptionAfterSave $event
                ) use (
                    $model,
                    $payload
                ): void {
                    self::assertSame(
                        $model,
                        $event->subscriptionModel,
                    );

                    self::assertSame(
                        $payload,
                        $event->savePayload,
                    );
                }
            );

        $service = new SaveSubscription(
            $transactionManager,
            TestConfig::$di->get(
                TransformSubscriptionModelToRecord::class,
            ),
            $saveNewRecord,
            $saveExistingRecord,
            $uuidFactory,
            $eventDispatcher,
        );

        $user     = new UserModel();
        $user->id = 'foo-user-id';

        $license     = new LicenseModel();
        $license->id = 'foo-license-id';

        $order1     = new OrderModel();
        $order1->id = 'foo-order-1-id';

        $order2     = new OrderModel();
        $order2->id = 'foo-order-2-id';

        $card     = new UserCardModel();
        $card->id = 'foo-card-id';

        $model->user = $user;

        $model->license = $license;

        $model->addOrder($order1);

        $model->addOrder($order2);

        $model->autoRenew = false;

        $model->card = $card;

        self::assertSame(
            $payload,
            $service($model),
        );
    }
}
