<?php

declare(strict_types=1);

namespace App\Orders\Services\Fetch\FetchOrdersByIds;

use App\Licenses\LicenseApi;
use App\Orders\Models\OrderItemModel;
use App\Orders\Models\OrderModel;
use App\Orders\Services\Fetch\Support\FetchOrderItemRecordsByOrderIds;
use App\Orders\Transformers\TransformOrderItemRecordToModel;
use App\Orders\Transformers\TransformOrderRecordToModel;
use App\Persistence\Orders\OrderItemRecord;
use App\Persistence\Orders\OrderRecord;
use App\Persistence\RecordQueryFactory;
use Throwable;

use function array_map;
use function count;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class FetchOrdersByIds
{
    private LicenseApi $licenseApi;
    private RecordQueryFactory $queryFactory;
    private FetchOrderItemRecordsByOrderIds $fetchOrderItemRecords;
    private TransformOrderRecordToModel $transformOrder;
    private TransformOrderItemRecordToModel $transformItem;

    public function __construct(
        LicenseApi $licenseApi,
        RecordQueryFactory $queryFactory,
        FetchOrderItemRecordsByOrderIds $fetchOrderItemRecords,
        TransformOrderRecordToModel $transformOrder,
        TransformOrderItemRecordToModel $transformItem
    ) {
        $this->licenseApi            = $licenseApi;
        $this->queryFactory          = $queryFactory;
        $this->fetchOrderItemRecords = $fetchOrderItemRecords;
        $this->transformOrder        = $transformOrder;
        $this->transformItem         = $transformItem;
    }

    /**
     * @param string[] $ids
     *
     * @return OrderModel[]
     */
    public function __invoke(array $ids): array
    {
        try {
            if (count($ids) < 1) {
                return [];
            }

            return $this->runInner($ids);
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * @param string[] $ids
     *
     * @return OrderModel[]
     */
    private function runInner(array $ids): array
    {
        /** @var OrderRecord[] $orderRecords */
        $orderRecords = ($this->queryFactory)(
            new OrderRecord()
        )
            ->withWhere('id', $ids, 'IN')
            ->withOrder('date', 'desc')
            ->all();

        if (count($orderRecords) < 1) {
            return [];
        }

        // TODO: Cover this with testing
        // @codeCoverageIgnoreStart

        $orderIds = array_map(
            static fn (OrderRecord $r) => $r->id,
            $orderRecords
        );

        $itemRecords = ($this->fetchOrderItemRecords)(
            $orderIds
        );

        $itemRecordsFlat = [];

        foreach ($itemRecords as $innerItemRecords) {
            foreach ($innerItemRecords as $itemRecord) {
                $itemRecordsFlat[] = $itemRecord;
            }
        }

        /** @psalm-suppress InvalidArgument */
        $licenseIds = array_map(
            static fn (OrderItemRecord $r) => $r->license_id,
            $itemRecordsFlat
        );

        $licenses = [];

        $licensesUnsorted = $this->licenseApi->fetchLicensesByIds(
            $licenseIds
        );

        foreach ($licensesUnsorted as $license) {
            $licenses[$license->id] = $license;
        }

        return array_map(
            function (OrderRecord $record) use (
                $itemRecords,
                $licenses
            ): OrderModel {
                $itemModels = array_map(
                    function (OrderItemRecord $item) use (
                        $licenses
                    ): OrderItemModel {
                        return ($this->transformItem)(
                            $item,
                            $licenses[$item->license_id] ?? null,
                        );
                    },
                    $itemRecords[$record->id] ?? [],
                );

                return ($this->transformOrder)(
                    $record,
                    $itemModels
                );
            },
            $orderRecords
        );

        // @codeCoverageIgnoreEnd
    }
}
