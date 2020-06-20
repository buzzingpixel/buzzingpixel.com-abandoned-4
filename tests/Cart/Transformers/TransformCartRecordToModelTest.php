<?php

declare(strict_types=1);

namespace Tests\Cart\Transformers;

use App\Cart\Models\CartItemModel;
use App\Cart\Transformers\TransformCartRecordToModel;
use App\Persistence\Cart\CartRecord;
use App\Persistence\Constants;
use App\Users\Models\UserModel;
use App\Users\Services\FetchUserById;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformCartRecordToModelTest extends TestCase
{
    public function test() : void
    {
        $user = new UserModel();

        $items = [new CartItemModel()];

        $createdAt = new DateTimeImmutable();

        $record                 = new CartRecord();
        $record->id             = 'foo-cart-id';
        $record->user_id        = 'foo-user-id';
        $record->total_items    = '456';
        $record->total_quantity = '789';
        $record->created_at     = $createdAt->format(
            Constants::POSTGRES_OUTPUT_FORMAT
        );

        $fetchUserById = $this->createMock(
            FetchUserById::class
        );

        $fetchUserById->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo('foo-user-id'))
            ->willReturn($user);

        $transformer = new TransformCartRecordToModel(
            $fetchUserById
        );

        $model = $transformer($record, $items);

        self::assertSame(
            'foo-cart-id',
            $model->id
        );

        self::assertSame($user, $model->user);

        self::assertSame(456, $model->totalItems);

        self::assertSame(789, $model->totalQuantity);

        self::assertSame($items, $model->items);

        self::assertSame(
            $createdAt->format(DateTimeInterface::ATOM),
            $model->createdAt->format(DateTimeInterface::ATOM),
        );
    }
}
