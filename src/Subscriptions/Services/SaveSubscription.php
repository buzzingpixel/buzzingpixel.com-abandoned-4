<?php

declare(strict_types=1);

namespace App\Subscriptions\Services;

use App\Payload\Payload;
use App\Persistence\DatabaseTransactionManager;
use App\Persistence\SaveExistingRecord;
use App\Persistence\SaveNewRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use App\Subscriptions\Events\SaveSubscriptionAfterSave;
use App\Subscriptions\Events\SaveSubscriptionBeforeSave;
use App\Subscriptions\Models\SubscriptionModel;
use App\Subscriptions\Transformers\TransformSubscriptionModelToRecord;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;

use function count;
use function in_array;

class SaveSubscription
{
    private DatabaseTransactionManager $transactionManager;
    private TransformSubscriptionModelToRecord $modelToRecord;
    private SaveNewRecord $saveNewRecord;
    private SaveExistingRecord $saveExistingRecord;
    private UuidFactoryWithOrderedTimeCodec $uuidFactory;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        DatabaseTransactionManager $transactionManager,
        TransformSubscriptionModelToRecord $modelToRecord,
        SaveNewRecord $saveNewRecord,
        SaveExistingRecord $saveExistingRecord,
        UuidFactoryWithOrderedTimeCodec $uuidFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->transactionManager = $transactionManager;
        $this->modelToRecord      = $modelToRecord;
        $this->saveNewRecord      = $saveNewRecord;
        $this->saveExistingRecord = $saveExistingRecord;
        $this->uuidFactory        = $uuidFactory;
        $this->eventDispatcher    = $eventDispatcher;
    }

    public function __invoke(SubscriptionModel $model): Payload
    {
        try {
            $this->transactionManager->beginTransaction();

            $payload = $this->innerRun($model);

            if (
                ! in_array(
                    $payload->getStatus(),
                    [
                        Payload::STATUS_UPDATED,
                        Payload::STATUS_CREATED,
                    ],
                    true,
                )
            ) {
                $this->transactionManager->rollBack();
            } else {
                $this->transactionManager->commit();
            }

            return $payload;
        } catch (Throwable $e) {
            $this->transactionManager->rollBack();

            return new Payload(Payload::STATUS_ERROR);
        }
    }

    /**
     * @throws Throwable
     */
    private function innerRun(SubscriptionModel $model): Payload
    {
        $errors = [];

        /** @psalm-suppress TypeDoesNotContainType */
        if (! isset($model->user)) {
            $errors[] = 'User must be set';
        }

        /** @psalm-suppress TypeDoesNotContainType */
        if (! isset($model->license)) {
            $errors[] = 'License must be set';
        }

        $beforeEvent = new SaveSubscriptionBeforeSave(
            $errors,
            $model
        );

        $this->eventDispatcher->dispatch($beforeEvent);

        if (count($beforeEvent->errors) > 0 || ! $beforeEvent->isValid) {
            return new Payload(
                Payload::STATUS_ERROR,
                ['errors' => $beforeEvent->errors]
            );
        }

        $isNew = false;

        if ($model->id === '') {
            $isNew = true;

            $model->id = $this->uuidFactory->uuid1()->toString();
        }

        $record = ($this->modelToRecord)($model);

        if ($isNew) {
            $payload = ($this->saveNewRecord)($record);
        } else {
            $payload = ($this->saveExistingRecord)($record);
        }

        $this->eventDispatcher->dispatch(new SaveSubscriptionAfterSave(
            $model,
            $payload
        ));

        return $payload;
    }
}
