<?php

declare(strict_types=1);

namespace App\Orders\Services\Fetch\FetchUsersOrders;

use App\Persistence\Orders\OrderRecord;
use App\Persistence\RecordQueryFactory;
use App\Users\Models\UserModel;

class FetchUserOrderRecords
{
    private RecordQueryFactory $recordQueryFactory;

    public function __construct(RecordQueryFactory $recordQueryFactory)
    {
        $this->recordQueryFactory = $recordQueryFactory;
    }

    /**
     * @return OrderRecord[]
     */
    public function __invoke(UserModel $user) : array
    {
        /** @var OrderRecord[] $result */
        $result = ($this->recordQueryFactory)(
            new OrderRecord()
        )
            ->withWhere('user_id', $user->id)
            ->withOrder('date', 'desc')
            ->all();

        return $result;
    }
}
