<?php

declare(strict_types=1);

namespace Tests\Cart\Services;

use App\Cart\Models\CartItemModel;
use App\Cart\Models\CartModel;
use App\Cart\Services\FetchCartByUserId;
use App\Cart\Transformers\TransformCartItemRecordToModel;
use App\Cart\Transformers\TransformCartRecordToModel;
use App\Persistence\Cart\CartItemRecord;
use App\Persistence\Cart\CartRecord;
use App\Persistence\Record;
use App\Persistence\RecordQuery;
use App\Persistence\RecordQueryFactory;
use Exception;
use PHPUnit\Framework\TestCase;

class FetchCartByUserIdTest extends TestCase
{
    public function testThrow() : void
    {
        $recordQuery = $this->createMock(RecordQuery::class);

        $recordQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('user_id'),
                self::equalTo('foo-id')
            )
            ->willThrowException(new Exception());

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (Record $record) use ($recordQuery) {
                    self::assertInstanceOf(
                        CartRecord::class,
                        $record
                    );

                    return $recordQuery;
                }
            );

        $transformCartItemRecordToModel = $this->createMock(
            TransformCartItemRecordToModel::class
        );

        $transformCartRecordToModel = $this->createMock(
            TransformCartRecordToModel::class
        );

        $service = new FetchCartByUserId(
            $recordQueryFactory,
            $transformCartItemRecordToModel,
            $transformCartRecordToModel,
        );

        self::assertNull($service('foo-id'));
    }

    public function testWhenNoRecord() : void
    {
        $recordQuery = $this->createMock(RecordQuery::class);

        $recordQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('user_id'),
                self::equalTo('foo-id')
            )
            ->willReturn($recordQuery);

        $recordQuery->expects(self::at(1))
            ->method('one')
            ->willReturn(null);

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (Record $record) use ($recordQuery) {
                    self::assertInstanceOf(
                        CartRecord::class,
                        $record
                    );

                    return $recordQuery;
                }
            );

        $transformCartItemRecordToModel = $this->createMock(
            TransformCartItemRecordToModel::class
        );

        $transformCartRecordToModel = $this->createMock(
            TransformCartRecordToModel::class
        );

        $service = new FetchCartByUserId(
            $recordQueryFactory,
            $transformCartItemRecordToModel,
            $transformCartRecordToModel,
        );

        self::assertNull($service('foo-id'));
    }

    public function test() : void
    {
        $cartRecord     = new CartRecord();
        $cartRecord->id = 'foo-id';

        $cartModel = new CartModel();

        $cartItemRecord1     = new CartItemRecord();
        $cartItemRecord1->id = 'foo-id-1';

        $cartItemModel1 = new CartItemModel();

        $cartItemRecord2     = new CartItemRecord();
        $cartItemRecord2->id = 'foo-id-2';

        $cartItemModel2 = new CartItemModel();

        $cartQuery = $this->createMock(RecordQuery::class);

        $cartQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('user_id'),
                self::equalTo('foo-id')
            )
            ->willReturn($cartQuery);

        $cartQuery->expects(self::at(1))
            ->method('one')
            ->willReturn($cartRecord);

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $itemQuery = $this->createMock(RecordQuery::class);

        $itemQuery->expects(self::at(0))
            ->method('withWhere')
            ->with(
                self::equalTo('cart_id'),
                self::equalTo('foo-id')
            )
            ->willReturn($itemQuery);

        $itemQuery->expects(self::at(1))
            ->method('withOrder')
            ->with(
                self::equalTo('item_slug'),
                self::equalTo('asc')
            )
            ->willReturn($itemQuery);

        $itemQuery->expects(self::at(2))
            ->method('withOrder')
            ->with(
                self::equalTo('id'),
                self::equalTo('asc')
            )
            ->willReturn($itemQuery);

        $itemQuery->expects(self::at(3))
            ->method('all')
            ->willReturn([
                $cartItemRecord1,
                $cartItemRecord2,
            ]);

        $recordQueryFactory->expects(self::at(0))
            ->method('__invoke')
            ->willReturnCallback(
                static function (Record $record) use ($cartQuery) {
                    self::assertInstanceOf(
                        CartRecord::class,
                        $record
                    );

                    return $cartQuery;
                }
            );

        $recordQueryFactory->expects(self::at(1))
            ->method('__invoke')
            ->willReturnCallback(
                static function (Record $record) use ($itemQuery) {
                    self::assertInstanceOf(
                        CartItemRecord::class,
                        $record
                    );

                    return $itemQuery;
                }
            );

        $transformCartItemRecordToModel = $this->createMock(
            TransformCartItemRecordToModel::class
        );

        $transformCartItemRecordToModel->expects(self::at(0))
            ->method('__invoke')
            ->willReturnCallback(
                static function (CartItemRecord $record) use (
                    $cartItemRecord1,
                    $cartItemModel1
                ) : CartItemModel {
                    self::assertSame($record, $cartItemRecord1);

                    return $cartItemModel1;
                }
            );

        $transformCartItemRecordToModel->expects(self::at(1))
            ->method('__invoke')
            ->willReturnCallback(
                static function (CartItemRecord $record) use (
                    $cartItemRecord2,
                    $cartItemModel2
                ) : CartItemModel {
                    self::assertSame($record, $cartItemRecord2);

                    return $cartItemModel2;
                }
            );

        $transformCartRecordToModel = $this->createMock(
            TransformCartRecordToModel::class
        );

        $transformCartRecordToModel->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    CartRecord $record,
                    array $items
                ) use (
                    $cartRecord,
                    $cartModel,
                    $cartItemModel1,
                    $cartItemModel2
                ) : CartModel {
                    self::assertSame($record, $cartRecord);

                    self::assertSame(
                        [$cartItemModel1, $cartItemModel2],
                        $items
                    );

                    $cartModel->addItem($cartItemModel1);
                    $cartModel->addItem($cartItemModel2);

                    return $cartModel;
                }
            );

        $service = new FetchCartByUserId(
            $recordQueryFactory,
            $transformCartItemRecordToModel,
            $transformCartRecordToModel,
        );

        $model = $service('foo-id');

        self::assertSame($cartModel, $model);

        self::assertCount(2, $model->items);

        foreach ($model->items as $key => $item) {
            self::assertSame(
                $key === 0 ? $cartItemModel1 : $cartItemModel2,
                $item
            );
        }
    }
}
