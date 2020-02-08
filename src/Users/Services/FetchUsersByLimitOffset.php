<?php

declare(strict_types=1);

namespace App\Users\Services;

use App\Persistence\RecordQueryFactory;
use App\Persistence\Users\UserRecord;
use App\Users\Models\UserModel;
use App\Users\Transformers\TransformUserRecordToUserModel;
use function array_map;

class FetchUsersByLimitOffset
{
    private RecordQueryFactory $recordQueryFactory;
    private TransformUserRecordToUserModel $transformUserRecordToUserModel;

    public function __construct(
        RecordQueryFactory $recordQueryFactory,
        TransformUserRecordToUserModel $transformUserRecordToUserModel
    ) {
        $this->recordQueryFactory             = $recordQueryFactory;
        $this->transformUserRecordToUserModel = $transformUserRecordToUserModel;
    }

    /**
     * @return UserModel[]
     *
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function __invoke(?int $limit = null, int $offset = 0) : array
    {
        $records = ($this->recordQueryFactory)(
            new UserRecord()
        )
            ->withOrder('last_name', 'asc')
            ->withOrder('first_name', 'asc')
            ->withOrder('email_address', 'asc')
            ->withLimit($limit)
            ->withOffset($offset)
            ->all();

        /** @psalm-suppress MixedReturnTypeCoercion */
        return array_map(
            $this->transformUserRecordToUserModel,
            $records
        );
    }
}
