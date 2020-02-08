<?php

declare(strict_types=1);

namespace App\Users\Services;

use App\Persistence\RecordQueryFactory;
use App\Persistence\Users\UserRecord;
use App\Users\Models\UserModel;
use App\Users\Transformers\TransformUserRecordToUserModel;
use function array_map;

class FetchUsersBySearch
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
    public function __invoke(string $queryString, ?int $limit = null, int $offset = 0) : array
    {
        $userRecordDummy = new UserRecord();

        $query = ($this->recordQueryFactory)(
            new UserRecord()
        );

        foreach ($userRecordDummy->getSearchableFields() as $field) {
            $query = $query->withNewWhereGroup(
                $field,
                $queryString,
                'LIKE'
            );
        }

        $records = $query->withOrder('last_name', 'asc')
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
