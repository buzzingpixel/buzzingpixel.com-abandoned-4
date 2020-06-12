<?php

declare(strict_types=1);

namespace App\Users\Services;

use App\Persistence\RecordQueryFactory;
use App\Persistence\UserCards\UserCardRecord;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use App\Users\Transformers\TransformUserCardRecordToModel;
use Throwable;
use function assert;

class FetchUserCardById
{
    private RecordQueryFactory $recordQueryFactory;
    private TransformUserCardRecordToModel $recordToModel;

    public function __construct(
        RecordQueryFactory $recordQueryFactory,
        TransformUserCardRecordToModel $recordToModel
    ) {
        $this->recordQueryFactory = $recordQueryFactory;
        $this->recordToModel      = $recordToModel;
    }

    public function __invoke(
        UserModel $user,
        string $id
    ) : ?UserCardModel {
        try {
            return $this->innerRun($user, $id);
        } catch (Throwable $e) {
            return null;
        }
    }

    private function innerRun(
        UserModel $user,
        string $id
    ) : ?UserCardModel {
        $record = ($this->recordQueryFactory)(
            new UserCardRecord()
        )
            ->withWhere('user_id', $user->id)
            ->withWhere('id', $id)
            ->one();

        assert($record instanceof UserCardRecord || $record === null);

        if ($record === null) {
            return null;
        }

        return ($this->recordToModel)($record, $user);
    }
}
