<?php

declare(strict_types=1);

namespace App\Users\Services;

use App\Payload\Payload;
use App\Persistence\SaveExistingRecord;
use App\Persistence\SaveNewRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use App\Users\Events\SaveUserCardAfterSave;
use App\Users\Events\SaveUserCardBeforeSave;
use App\Users\Models\UserCardModel;
use App\Users\Transformers\TransformUserCardModelToRecord;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;
use function mb_substr;

class SaveUserCard
{
    private SaveNewRecord $saveNewRecord;
    private SaveExistingRecord $saveExistingRecord;
    private UuidFactoryWithOrderedTimeCodec $uuidFactory;
    private TransformUserCardModelToRecord $modelToRecord;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        SaveNewRecord $saveNewRecord,
        SaveExistingRecord $saveExistingRecord,
        UuidFactoryWithOrderedTimeCodec $uuidFactory,
        TransformUserCardModelToRecord $modelToRecord,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->saveNewRecord      = $saveNewRecord;
        $this->saveExistingRecord = $saveExistingRecord;
        $this->uuidFactory        = $uuidFactory;
        $this->modelToRecord      = $modelToRecord;
        $this->eventDispatcher    = $eventDispatcher;
    }

    public function __invoke(UserCardModel $userCard) : Payload
    {
        try {
            return $this->innerRun($userCard);
        } catch (Throwable $e) {
            return new Payload(
                Payload::STATUS_ERROR,
                ['message' => 'An unknown error occurred']
            );
        }
    }

    public function innerRun(UserCardModel $userCard) : Payload
    {
        $beforeSaveEvent = new SaveUserCardBeforeSave(
            $userCard
        );

        $this->eventDispatcher->dispatch($beforeSaveEvent);

        if (! $beforeSaveEvent->isValid) {
            return new Payload(
                Payload::STATUS_NOT_VALID,
                ['message' => 'The provided card is not valid']
            );
        }

        if ($userCard->newCardNumber !== '') {
            $userCard->lastFour = mb_substr(
                $userCard->newCardNumber,
                -4,
            );
        }

        if ($userCard->id !== '') {
            $payload = ($this->saveExistingRecord)(
                ($this->modelToRecord)($userCard)
            );

            $afterSaveEvent = new SaveUserCardAfterSave(
                $userCard,
                $payload,
            );

            $this->eventDispatcher->dispatch($afterSaveEvent);

            return $payload;
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $userCard->id = $this->uuidFactory->uuid1()->toString();

        $payload = ($this->saveNewRecord)(
            ($this->modelToRecord)($userCard)
        );

        $afterSaveEvent = new SaveUserCardAfterSave(
            $userCard,
            $payload,
        );

        $this->eventDispatcher->dispatch($afterSaveEvent);

        return $payload;
    }
}
