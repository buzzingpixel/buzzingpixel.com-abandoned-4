<?php

declare(strict_types=1);

namespace App\Orders\Services\Fetch;

use App\Licenses\LicenseApi;
use App\Orders\Models\OrderItemModel;
use App\Orders\Models\OrderModel;
use App\Orders\Transformers\TransformOrderItemRecordToModel;
use App\Orders\Transformers\TransformOrderRecordToModel;
use App\Persistence\Orders\OrderItemRecord;
use App\Persistence\Orders\OrderRecord;
use App\Users\Models\UserModel;
use Throwable;
use function array_map;
use function count;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class FetchUsersOrdersMaster
{
    private FetchUserOrderRecords $fetchUserOrderRecords;
    private FetchOrderItemRecordsByOrderIds $fetchOrderItemRecords;
    private LicenseApi $licenseApi;
    private TransformOrderRecordToModel $transformOrder;
    private TransformOrderItemRecordToModel $transformItem;

    public function __construct(
        FetchUserOrderRecords $fetchUserOrderRecords,
        FetchOrderItemRecordsByOrderIds $fetchOrderItemRecords,
        LicenseApi $licenseApi,
        TransformOrderRecordToModel $transformOrder,
        TransformOrderItemRecordToModel $transformItem
    ) {
        $this->fetchUserOrderRecords = $fetchUserOrderRecords;
        $this->fetchOrderItemRecords = $fetchOrderItemRecords;
        $this->licenseApi            = $licenseApi;
        $this->transformOrder        = $transformOrder;
        $this->transformItem         = $transformItem;
    }

    /**
     * @return OrderModel[]
     */
    public function __invoke(UserModel $user) : array
    {
        try {
            $orderRecords = ($this->fetchUserOrderRecords)($user);

            if (count($orderRecords) < 1) {
                return [];
            }

            $orderIds = array_map(
                static fn(OrderRecord $record) => $record->id,
                $orderRecords
            );

            $itemRecords = ($this->fetchOrderItemRecords)(
                $orderIds
            );

            $licenses = [];

            $licensesUnsorted = $this->licenseApi->fetchUserLicenses(
                $user
            );

            foreach ($licensesUnsorted as $license) {
                $licenses[$license->id] = $license;
            }

            return array_map(
                function (OrderRecord $record) use (
                    $itemRecords,
                    $licenses,
                    $user
                ) : OrderModel {
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
                },
                $orderRecords
            );
        } catch (Throwable $e) {
            return [];
        }
    }
}
