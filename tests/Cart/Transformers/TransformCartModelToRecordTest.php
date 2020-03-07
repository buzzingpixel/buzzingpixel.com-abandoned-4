<?php

declare(strict_types=1);

namespace Tests\Cart\Transformers;

use App\Cart\Models\CartModel;
use App\Cart\Transformers\TransformCartModelToRecord;
use App\Users\Models\UserModel;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformCartModelToRecordTest extends TestCase
{
    public function test() : void
    {
        $user     = new UserModel();
        $user->id = 'foo-user-id';

        $cartModel                = new CartModel();
        $cartModel->id            = 'foo-cart-id';
        $cartModel->user          = $user;
        $cartModel->totalItems    = 3;
        $cartModel->totalQuantity = 4;

        $transformer = new TransformCartModelToRecord();

        $record = $transformer($cartModel);

        self::assertSame(
            'foo-cart-id',
            $record->id,
        );

        self::assertSame(
            'foo-user-id',
            $record->user_id,
        );

        self::assertSame(
            '3',
            $record->total_items,
        );

        self::assertSame(
            '4',
            $record->total_quantity,
        );

        self::assertSame(
            $cartModel->createdAt->format(
                DateTimeInterface::ATOM
            ),
            $record->created_at,
        );
    }
}
