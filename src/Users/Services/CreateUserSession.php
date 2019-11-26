<?php

declare(strict_types=1);

namespace App\Users\Services;

use App\Payload\Payload;
use App\Persistence\SaveNewRecord;
use App\Persistence\Users\UserSessionRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use App\Users\Models\UserModel;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

class CreateUserSession
{
    /** @var UuidFactoryWithOrderedTimeCodec */
    private $uuidFactory;
    /** @var SaveNewRecord */
    private $saveNewRecord;

    public function __construct(
        UuidFactoryWithOrderedTimeCodec $uuidFactory,
        SaveNewRecord $saveNewRecord
    ) {
        $this->uuidFactory   = $uuidFactory;
        $this->saveNewRecord = $saveNewRecord;
    }

    public function __invoke(UserModel $user) : Payload
    {
        if ($user->getId() === '') {
            return new Payload(
                Payload::STATUS_NOT_CREATED,
                ['message' => 'User ID is required']
            );
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $currentDate = new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC')
        );

        $record = new UserSessionRecord();

        /** @noinspection PhpUnhandledExceptionInspection */
        $record->id = $this->uuidFactory->uuid1()->toString();

        $record->user_id = $user->getId();

        $record->created_at = $currentDate->format(DateTimeInterface::ATOM);

        $record->last_touched_at = $currentDate->format(DateTimeInterface::ATOM);

        return ($this->saveNewRecord)($record);
    }
}
