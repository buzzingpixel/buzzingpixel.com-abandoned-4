<?php

declare(strict_types=1);

namespace App\Users\Services;

use App\Payload\Payload;
use App\Persistence\SaveExistingRecord;
use App\Persistence\SaveNewRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use App\Users\Models\UserCardModel;
use App\Users\Transformers\TransformUserCardModelToRecord;
use Throwable;

class SaveUserCard
{
    private SaveNewRecord $saveNewRecord;
    private SaveExistingRecord $saveExistingRecord;
    private UuidFactoryWithOrderedTimeCodec $uuidFactory;
    private TransformUserCardModelToRecord $modelToRecord;

    public function __construct(
        SaveNewRecord $saveNewRecord,
        SaveExistingRecord $saveExistingRecord,
        UuidFactoryWithOrderedTimeCodec $uuidFactory,
        TransformUserCardModelToRecord $modelToRecord
    ) {
        $this->saveNewRecord      = $saveNewRecord;
        $this->saveExistingRecord = $saveExistingRecord;
        $this->uuidFactory        = $uuidFactory;
        $this->modelToRecord      = $modelToRecord;
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
        if ($userCard->id !== '') {
            return ($this->saveExistingRecord)(
                ($this->modelToRecord)($userCard)
            );
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $userCard->id = $this->uuidFactory->uuid1()->toString();

        return ($this->saveNewRecord)(
            ($this->modelToRecord)($userCard)
        );
    }
}
