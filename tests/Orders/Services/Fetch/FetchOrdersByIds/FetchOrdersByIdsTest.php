<?php

declare(strict_types=1);

namespace Tests\Orders\Services\Fetch\FetchOrdersByIds;

use _HumbugBox89320708a2e3\Nette\Neon\Exception;
use App\Licenses\LicenseApi;
use App\Licenses\Models\LicenseModel;
use App\Orders\Services\Fetch\FetchOrdersByIds\FetchOrdersByIds;
use App\Orders\Services\Fetch\Support\FetchOrderItemRecordsByOrderIds;
use App\Orders\Transformers\TransformOrderItemRecordToModel;
use App\Orders\Transformers\TransformOrderRecordToModel;
use App\Persistence\Constants;
use App\Persistence\Orders\OrderItemRecord;
use App\Persistence\Orders\OrderRecord;
use App\Persistence\RecordQuery;
use App\Persistence\RecordQueryFactory;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class FetchOrdersByIdsTest extends TestCase
{
    public function testWhenThrows(): void
    {
        $licenseApi = $this->createMock(LicenseApi::class);

        $licenseApi->expects(self::never())
            ->method(self::anything());

        $queryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $queryFactory->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo(new OrderRecord()))
            ->willThrowException(new Exception());

        $fetchOrderItemRecords = $this->createMock(
            FetchOrderItemRecordsByOrderIds::class
        );

        $fetchOrderItemRecords->expects(self::never())
            ->method(self::anything());

        $transformOrder = $this->createMock(
            TransformOrderRecordToModel::class
        );

        $transformOrder->expects(self::never())
            ->method(self::anything());

        $transformItem = $this->createMock(
            TransformOrderItemRecordToModel::class
        );

        $transformItem->expects(self::never())
            ->method(self::anything());

        $service = new FetchOrdersByIds(
            $licenseApi,
            $queryFactory,
            $fetchOrderItemRecords,
            $transformOrder,
            $transformItem,
        );

        self::assertSame([], $service(['foo-id']));
    }

    public function testWhenNoIds(): void
    {
        $licenseApi = $this->createMock(LicenseApi::class);

        $licenseApi->expects(self::never())
            ->method(self::anything());

        $queryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $queryFactory->expects(self::never())
            ->method(self::anything());

        $fetchOrderItemRecords = $this->createMock(
            FetchOrderItemRecordsByOrderIds::class
        );

        $fetchOrderItemRecords->expects(self::never())
            ->method(self::anything());

        $transformOrder = $this->createMock(
            TransformOrderRecordToModel::class
        );

        $transformOrder->expects(self::never())
            ->method(self::anything());

        $transformItem = $this->createMock(
            TransformOrderItemRecordToModel::class
        );

        $transformItem->expects(self::never())
            ->method(self::anything());

        $service = new FetchOrdersByIds(
            $licenseApi,
            $queryFactory,
            $fetchOrderItemRecords,
            $transformOrder,
            $transformItem,
        );

        self::assertSame([], $service([]));
    }

    public function testWhenNoRecords(): void
    {
        $licenseApi = $this->createMock(LicenseApi::class);

        $licenseApi->expects(self::never())
            ->method(self::anything());

        $query = $this->createMock(RecordQuery::class);

        $query->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('id'),
                self::equalTo(['foo-input-id']),
                self::equalTo('IN'),
            )
            ->willReturn($query);

        $query->expects(self::at(1))
            ->method('withOrder')
            ->with(
                self::equalTo('date'),
                self::equalTo('desc'),
            )
            ->willReturn($query);

        $query->expects(self::at(2))
            ->method('all')
            ->willReturn([]);

        $queryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $queryFactory->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo(new OrderRecord()))
            ->willReturn($query);

        $fetchOrderItemRecords = $this->createMock(
            FetchOrderItemRecordsByOrderIds::class
        );

        $fetchOrderItemRecords->expects(self::never())
            ->method(self::anything());

        $transformOrder = $this->createMock(
            TransformOrderRecordToModel::class
        );

        $transformOrder->expects(self::never())
            ->method(self::anything());

        $transformItem = $this->createMock(
            TransformOrderItemRecordToModel::class
        );

        $transformItem->expects(self::never())
            ->method(self::anything());

        $service = new FetchOrdersByIds(
            $licenseApi,
            $queryFactory,
            $fetchOrderItemRecords,
            $transformOrder,
            $transformItem,
        );

        self::assertSame([], $service(['foo-input-id']));
    }

    public function test(): void
    {
        // TODO: Actually do this test
        $this->expectNotToPerformAssertions();

        return;

        /** @phpstan-ignore-next-line */
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

        $licenseApi = $this->createMock(LicenseApi::class);

        $licenseApi->expects(self::once())
            ->method('fetchLicensesByIds')
            ->with(self::equalTo([
                $license1->id,
                $license2->id,
                $license3->id,
            ]))
            ->willReturn($licenses);

        $query = $this->createMock(RecordQuery::class);

        $query->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('id'),
                self::equalTo(['foo-input-id']),
                self::equalTo('IN'),
            )
            ->willReturn($query);

        $query->expects(self::at(1))
            ->method('withOrder')
            ->with(
                self::equalTo('date'),
                self::equalTo('desc'),
            )
            ->willReturn($query);

        $query->expects(self::at(2))
            ->method('all')
            ->willReturn($orderRecords);

        $queryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $queryFactory->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo(new OrderRecord()))
            ->willReturn($query);

        $fetchOrderItemRecords = $this->createMock(
            FetchOrderItemRecordsByOrderIds::class
        );

        $fetchOrderItemRecords->expects(self::once())
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

        $transformOrder = $this->createMock(
            TransformOrderRecordToModel::class
        );

        $transformOrder->expects(self::never())
            ->method(self::anything());

        $transformItem = $this->createMock(
            TransformOrderItemRecordToModel::class
        );

        $transformItem->expects(self::never())
            ->method(self::anything());

        $service = new FetchOrdersByIds(
            $licenseApi,
            $queryFactory,
            $fetchOrderItemRecords,
            $transformOrder,
            $transformItem,
        );

        self::assertSame([], $service(['foo-input-id']));
    }
}
