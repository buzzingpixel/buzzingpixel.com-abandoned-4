<?php

declare(strict_types=1);

namespace App\Orders\Services\Fetch\FetchUserOrderByid;

use App\Licenses\LicenseApi;
use App\Orders\Models\OrderItemModel;
use App\Orders\Models\OrderModel;
use App\Orders\Services\Fetch\Support\FetchOrderItemRecordsByOrderIds;
use App\Orders\Transformers\TransformOrderItemRecordToModel;
use App\Orders\Transformers\TransformOrderRecordToModel;
use App\Persistence\Orders\OrderItemRecord;
use App\Persistence\Orders\OrderRecord;
use App\Persistence\RecordQueryFactory;
use App\Users\Models\UserModel;
use Throwable;
use function array_map;
use function assert;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class FetchUserOrderById
{
    private RecordQueryFactory $recordQueryFactory;
    private FetchOrderItemRecordsByOrderIds $fetchOrderItemRecords;
    private LicenseApi $licenseApi;
    private TransformOrderRecordToModel $transformOrder;
    private TransformOrderItemRecordToModel $transformItem;

    public function __construct(
        RecordQueryFactory $recordQueryFactory,
        FetchOrderItemRecordsByOrderIds $fetchOrderItemRecords,
        LicenseApi $licenseApi,
        TransformOrderRecordToModel $transformOrder,
        TransformOrderItemRecordToModel $transformItem
    ) {
        $this->recordQueryFactory    = $recordQueryFactory;
        $this->fetchOrderItemRecords = $fetchOrderItemRecords;
        $this->licenseApi            = $licenseApi;
        $this->transformOrder        = $transformOrder;
        $this->transformItem         = $transformItem;
    }

    public function __invoke(UserModel $user, string $id) : ?OrderModel
    {
        try {
            $record = ($this->recordQueryFactory)(
                new OrderRecord()
            )
                ->withWhere('user_id', $user->id)
                ->withWhere('id', $id)
                ->one();

            if ($record === null) {
                return null;
            }

            assert($record instanceof OrderRecord);

            $itemRecords = ($this->fetchOrderItemRecords)(
                [$record->id]
            );

            $licenses = [];

            $licensesUnsorted = $this->licenseApi->fetchUserLicenses(
                $user
            );

            foreach ($licensesUnsorted as $license) {
                $licenses[$license->id] = $license;
            }

            $itemModels = array_map(
                function (OrderItemRecord $item) use (
                    $licenses
                ) : OrderItemModel {
                    return ($this->transformItem)(
                        $item,
                        $licenses[$item->license_id] ?? null,
                    );
                },
                $itemRecords[$record->id] ?? [],
            );

            return ($this->transformOrder)(
                $record,
                $itemModels,
                $user
            );
        } catch (Throwable $e) {
            return null;
        }
    }
}
