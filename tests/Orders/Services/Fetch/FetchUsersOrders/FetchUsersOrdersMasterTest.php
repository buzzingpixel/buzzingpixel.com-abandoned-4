<?php

declare(strict_types=1);

namespace Tests\Orders\Services\Fetch\FetchUsersOrders;

use App\Licenses\LicenseApi;
use App\Licenses\Models\LicenseModel;
use App\Orders\Services\Fetch\FetchUsersOrders\FetchUserOrderRecords;
use App\Orders\Services\Fetch\FetchUsersOrders\FetchUsersOrdersMaster;
use App\Orders\Services\Fetch\Support\FetchOrderItemRecordsByOrderIds;
use App\Orders\Transformers\TransformOrderItemRecordToModel;
use App\Orders\Transformers\TransformOrderRecordToModel;
use App\Persistence\Constants;
use App\Persistence\Orders\OrderItemRecord;
use App\Persistence\Orders\OrderRecord;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class FetchUsersOrdersMasterTest extends TestCase
{
    public function testWhenExceptionThrown() : void
    {
        $user = new UserModel();

        $fetchUserOrderRecords = $this->createMock(
            FetchUserOrderRecords::class
        );

        $fetchUserOrderRecords->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($user))
            ->willThrowException(new Exception());

        $fetchOrderItemRecordsByOrderIds = $this->createMock(
            FetchOrderItemRecordsByOrderIds::class
        );

        $fetchOrderItemRecordsByOrderIds->expects(self::never())
            ->method(self::anything());

        $licenseApi = $this->createMock(
            LicenseApi::class
        );

        $licenseApi->expects(self::never())
            ->method(self::anything());

        $transformOrderRecordToModel = $this->createMock(
            TransformOrderRecordToModel::class
        );

        $transformOrderRecordToModel->expects(self::never())
            ->method(self::anything());

        $transformOrderItemRecordToModel = $this->createMock(
            TransformOrderItemRecordToModel::class
        );

        $transformOrderItemRecordToModel->expects(self::never())
            ->method(self::anything());

        $service = new FetchUsersOrdersMaster(
            $fetchUserOrderRecords,
            $fetchOrderItemRecordsByOrderIds,
            $licenseApi,
            $transformOrderRecordToModel,
            $transformOrderItemRecordToModel,
        );

        self::assertSame([], $service($user));
    }

    public function testWhenNoRecords() : void
    {
        $user = new UserModel();

        $fetchUserOrderRecords = $this->createMock(
            FetchUserOrderRecords::class
        );

        $fetchUserOrderRecords->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($user))
            ->willReturn([]);

        $fetchOrderItemRecordsByOrderIds = $this->createMock(
            FetchOrderItemRecordsByOrderIds::class
        );

        $fetchOrderItemRecordsByOrderIds->expects(self::never())
            ->method(self::anything());

        $licenseApi = $this->createMock(
            LicenseApi::class
        );

        $licenseApi->expects(self::never())
            ->method(self::anything());

        $transformOrderRecordToModel = $this->createMock(
            TransformOrderRecordToModel::class
        );

        $transformOrderRecordToModel->expects(self::never())
            ->method(self::anything());

        $transformOrderItemRecordToModel = $this->createMock(
            TransformOrderItemRecordToModel::class
        );

        $transformOrderItemRecordToModel->expects(self::never())
            ->method(self::anything());

        $service = new FetchUsersOrdersMaster(
            $fetchUserOrderRecords,
            $fetchOrderItemRecordsByOrderIds,
            $licenseApi,
            $transformOrderRecordToModel,
            $transformOrderItemRecordToModel,
        );

        self::assertSame([], $service($user));
    }

    public function test() : void
    {
        $user = new UserModel();

        $license1     = new LicenseModel();
        $license1->id = 'foo-license-id-1';

        $license2     = new LicenseModel();
        $license2->id = 'foo-license-id-2';

        $license3     = new LicenseModel();
        $license3->id = 'foo-license-id-3';

        $licenses = [
            $license1,
            $license2,
            $license3,
        ];

        $orderRecord1       = new OrderRecord();
        $orderRecord1->id   = 'foo-order-id-1';
        $orderRecord1->date = (new DateTimeImmutable())->format(
            Constants::POSTGRES_OUTPUT_FORMAT
        );

        $orderRecord2       = new OrderRecord();
        $orderRecord2->id   = 'foo-order-id-2';
        $orderRecord2->date = (new DateTimeImmutable())->format(
            Constants::POSTGRES_OUTPUT_FORMAT
        );

        $orderRecords = [
            $orderRecord1,
            $orderRecord2,
        ];

        $orderItemRecord1             = new OrderItemRecord();
        $orderItemRecord1->id         = 'foo-order-item-id-1';
        $orderItemRecord1->license_id = 'foo-license-id-1';

        $orderItemRecord2             = new OrderItemRecord();
        $orderItemRecord2->id         = 'foo-order-item-id-2';
        $orderItemRecord2->license_id = 'foo-license-id-2';

        $orderItemRecord3             = new OrderItemRecord();
        $orderItemRecord3->id         = 'foo-order-item-id-3';
        $orderItemRecord3->license_id = 'foo-license-id-3';

        $fetchUserOrderRecords = $this->createMock(
            FetchUserOrderRecords::class
        );

        $fetchUserOrderRecords->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($user))
            ->willReturn($orderRecords);

        $fetchOrderItemRecordsByOrderIds = $this->createMock(
            FetchOrderItemRecordsByOrderIds::class
        );

        $fetchOrderItemRecordsByOrderIds->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo([
                'foo-order-id-1',
                'foo-order-id-2',
            ]))
            ->willReturn([
                'foo-order-id-1' => [
                    $orderItemRecord1,
                    $orderItemRecord2,
                ],
                'foo-order-id-2' => [$orderItemRecord3],
            ]);

        $licenseApi = $this->createMock(
            LicenseApi::class
        );

        $licenseApi->expects(self::once())
            ->method('fetchUserLicenses')
            ->with(self::equalTo($user))
            ->willReturn($licenses);

        $transformOrderRecordToModel = new TransformOrderRecordToModel(
            $this->createMock(UserApi::class)
        );

        $transformOrderItemRecordToModel = new TransformOrderItemRecordToModel(
            $this->createMock(LicenseApi::class)
        );

        $service = new FetchUsersOrdersMaster(
            $fetchUserOrderRecords,
            $fetchOrderItemRecordsByOrderIds,
            $licenseApi,
            $transformOrderRecordToModel,
            $transformOrderItemRecordToModel,
        );

        $orders = $service($user);

        self::assertCount(2, $orders);

        $returnOrder1 = $orders[0];
        $returnOrder2 = $orders[1];
        self::assertSame($orderRecord1->id, $returnOrder1->id);
        self::assertSame($orderRecord2->id, $returnOrder2->id);

        $returnItems1 = $returnOrder1->items;
        self::assertCount(2, $returnItems1);
        $returnItem1 = $returnItems1[0];
        $returnItem2 = $returnItems1[1];
        self::assertSame(
            $orderItemRecord1->id,
            $returnItem1->id
        );
        self::assertSame($returnItem1->license, $license1);
        self::assertSame(
            $orderItemRecord2->id,
            $returnItem2->id
        );
        self::assertSame($returnItem2->license, $license2);

        $returnItems2 = $returnOrder2->items;
        self::assertCount(1, $returnItems2);
        $returnItem3 = $returnItems2[0];
        self::assertSame(
            $orderItemRecord3->id,
            $returnItem3->id
        );
        self::assertSame($returnItem3->license, $license3);
    }
}
