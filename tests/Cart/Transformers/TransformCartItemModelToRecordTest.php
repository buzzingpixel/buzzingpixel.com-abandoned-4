<?php

declare(strict_types=1);

namespace Tests\Cart\Transformers;

use App\Cart\Models\CartItemModel;
use App\Cart\Models\CartModel;
use App\Cart\Transformers\TransformCartItemModelToRecord;
use App\Software\Models\SoftwareModel;
use PHPUnit\Framework\TestCase;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformCartItemModelToRecordTest extends TestCase
{
    public function test() : void
    {
        $cart     = new CartModel();
        $cart->id = 'foo-cart-id';

        $software       = new SoftwareModel();
        $software->slug = 'foo-software-slug';

        $cartItemModel           = new CartItemModel();
        $cartItemModel->id       = 'foo-id';
        $cartItemModel->cart     = $cart;
        $cartItemModel->software = $software;
        $cartItemModel->quantity = 2;

        $transformer = new TransformCartItemModelToRecord();

        $cartItemRecord = $transformer($cartItemModel);

        self::assertSame(
            'foo-id',
            $cartItemRecord->id
        );

        self::assertSame(
            'foo-cart-id',
            $cartItemRecord->cart_id
        );

        self::assertSame(
            'foo-software-slug',
            $cartItemRecord->item_slug
        );

        self::assertSame(
            '2',
            $cartItemRecord->quantity
        );
    }
}
