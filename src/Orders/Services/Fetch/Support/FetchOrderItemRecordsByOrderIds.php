<?php

declare(strict_types=1);

namespace App\Orders\Services\Fetch\Support;

use App\Persistence\Orders\OrderItemRecord;
use App\Persistence\RecordQueryFactory;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class FetchOrderItemRecordsByOrderIds
{
    private RecordQueryFactory $recordQueryFactory;

    public function __construct(
        RecordQueryFactory $recordQueryFactory
    ) {
        $this->recordQueryFactory = $recordQueryFactory;
    }

    /**
     * @param string[] $orderIds
     *
     * @return array<string, OrderItemRecord[]>
     *
     * @noinspection PhpDocSignatureInspection
     */
    public function __invoke(array $orderIds) : array
    {
        /** @var OrderItemRecord[] $itemRecords */
        $itemRecords = ($this->recordQueryFactory)(
            new OrderItemRecord()
        )
            ->withWhere('order_id', $orderIds, 'IN')
            ->withOrder('item_title', 'desc')
            ->withOrder('item_key', 'desc')
            ->withOrder('major_version', 'desc')
            ->withOrder('version', 'desc')
            ->all();

        $itemRecordsSorted = [];

        foreach ($itemRecords as $itemRecord) {
            $itemRecordsSorted[$itemRecord->order_id][] = $itemRecord;
        }

        return $itemRecordsSorted;
    }
}
