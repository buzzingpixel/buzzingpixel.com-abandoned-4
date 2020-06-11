<?php

declare(strict_types=1);

namespace App\Users\Services;

use App\Persistence\RecordQueryFactory;
use App\Persistence\UserCards\UserCardRecord;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use App\Users\Transformers\TransformUserCardRecordToModel;
use function array_map;

class FetchUserCards
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

    /**
     * @return UserCardModel[]
     */
    public function __invoke(UserModel $user) : array
    {
        /** @var UserCardRecord[] $records */
        $records = ($this->recordQueryFactory)(
            new UserCardRecord()
        )
            ->withWhere('user_id', $user->id)
            ->withOrder('is_default', 'desc')
            ->withOrder('nickname', 'asc')
            ->all();

        return array_map(
            fn(UserCardRecord $record) => ($this->recordToModel)(
                $record,
                $user
            ),
            $records
        );
    }
}
