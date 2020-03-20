<?php

declare(strict_types=1);

namespace Tests\Orders\Services\Fetch\FetchUserOrderByid;

use App\Licenses\LicenseApi;
use App\Licenses\Models\LicenseModel;
use App\Orders\Models\OrderModel;
use App\Orders\Services\Fetch\FetchUserOrderByid\FetchUserOrderById;
use App\Orders\Services\Fetch\Support\FetchOrderItemRecordsByOrderIds;
use App\Orders\Transformers\TransformOrderItemRecordToModel;
use App\Orders\Transformers\TransformOrderRecordToModel;
use App\Persistence\Constants;
use App\Persistence\Orders\OrderItemRecord;
use App\Persistence\Orders\OrderRecord;
use App\Persistence\RecordQuery;
use App\Persistence\RecordQueryFactory;
use App\Users\Models\UserModel;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;
use Tests\TestConfig;
use function assert;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class FetchUserOrderByIdTest extends TestCase
{
    public function testWhenExceptionThrown() : void
    {
        $licenseApi = $this->createMock(LicenseApi::class);

        $licenseApi->expects(self::never())
            ->method(self::anything());

        $fetchOrderItemRecords = $this->createMock(
            FetchOrderItemRecordsByOrderIds::class
        );

        $fetchOrderItemRecords->expects(self::never())
            ->method(self::anything());

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo(new OrderRecord()))
            ->willThrowException(new Exception());

        $service = new FetchUserOrderById(
            $recordQueryFactory,
            $fetchOrderItemRecords,
            $licenseApi,
            TestConfig::$di->get(
                TransformOrderRecordToModel::class
            ),
            TestConfig::$di->get(
                TransformOrderItemRecordToModel::class
            ),
        );

        self::assertNull($service(new UserModel(), 'foo-id'));
    }

    public function testWhenNoRecords() : void
    {
        $user     = new UserModel();
        $user->id = 'foo-user-id';

        $licenseApi = $this->createMock(LicenseApi::class);

        $licenseApi->expects(self::never())
            ->method(self::anything());

        $fetchOrderItemRecords = $this->createMock(
            FetchOrderItemRecordsByOrderIds::class
        );

        $fetchOrderItemRecords->expects(self::never())
            ->method(self::anything());

        $recordQuery = $this->createMock(RecordQuery::class);

        $recordQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('user_id'),
                self::equalTo('foo-user-id'),
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(1))
            ->method('withWhere')
            ->with(
                self::equalTo('id'),
                self::equalTo('foo-id'),
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(2))
            ->method('one')
            ->with()
            ->willReturn(null);

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo(new OrderRecord()))
            ->willReturn($recordQuery);

        $service = new FetchUserOrderById(
            $recordQueryFactory,
            $fetchOrderItemRecords,
            $licenseApi,
            TestConfig::$di->get(
                TransformOrderRecordToModel::class
            ),
            TestConfig::$di->get(
                TransformOrderItemRecordToModel::class
            ),
        );

        self::assertNull($service($user, 'foo-id'));
    }

    public function test() : void
    {
        $record          = new OrderRecord();
        $record->id      = 'foo-id';
        $record->user_id = 'foo-user-id';
        $record->date    = (new DateTimeImmutable())->format(
            Constants::POSTGRES_OUTPUT_FORMAT
        );

        $user     = new UserModel();
        $user->id = 'foo-user-id';

        $license1     = new LicenseModel();
        $license1->id = 'foo-license-id-1';

        $license2     = new LicenseModel();
        $license2->id = 'foo-license-id-2';

        $licenses = [$license1, $license2];

        $orderItemRecord1             = new OrderItemRecord();
        $orderItemRecord1->id         = 'foo-order-item-id-1';
        $orderItemRecord1->license_id = 'foo-license-id-1';

        $orderItemRecord2             = new OrderItemRecord();
        $orderItemRecord2->id         = 'foo-order-item-id-2';
        $orderItemRecord2->license_id = 'foo-license-id-2';

        $licenseApi = $this->createMock(LicenseApi::class);

        $licenseApi->expects(self::once())
            ->method('fetchUserLicenses')
            ->with(self::equalTo($user))
            ->willReturn($licenses);

        $fetchOrderItemRecords = $this->createMock(
            FetchOrderItemRecordsByOrderIds::class
        );

        $fetchOrderItemRecords->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo([$record->id]))
            ->willReturn([
                'foo-id' => [$orderItemRecord1, $orderItemRecord2],
            ]);

        $recordQuery = $this->createMock(RecordQuery::class);

        $recordQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('user_id'),
                self::equalTo('foo-user-id'),
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(1))
            ->method('withWhere')
            ->with(
                self::equalTo('id'),
                self::equalTo('foo-id'),
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(2))
            ->method('one')
            ->with()
            ->willReturn($record);

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo(new OrderRecord()))
            ->willReturn($recordQuery);

        $service = new FetchUserOrderById(
            $recordQueryFactory,
            $fetchOrderItemRecords,
            $licenseApi,
            TestConfig::$di->get(
                TransformOrderRecordToModel::class
            ),
            TestConfig::$di->get(
                TransformOrderItemRecordToModel::class
            ),
        );

        $orderModel = $service($user, 'foo-id');

        assert($orderModel instanceof OrderModel);

        self::assertCount(2, $orderModel->items);

        $returnItem1 = $orderModel->items[0];
        self::assertSame(
            $orderItemRecord1->id,
            $returnItem1->id
        );
        self::assertSame(
            $license1->id,
            $returnItem1->license->id
        );
    }
}
