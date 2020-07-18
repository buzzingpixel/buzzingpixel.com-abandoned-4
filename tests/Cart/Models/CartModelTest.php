<?php

declare(strict_types=1);

namespace Tests\Cart\Models;

use App\Cart\Models\CartItemModel;
use App\Cart\Models\CartModel;
use App\Software\Models\SoftwareModel;
use App\Users\Models\UserModel;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class CartModelTest extends TestCase
{
    public function testIncorrectPropertySet(): void
    {
        $model = new CartModel();

        self::expectException(RuntimeException::class);

        self::expectExceptionMessage('Invalid property');

        $model->asdf = 'asdf';
    }

    public function testIsset(): void
    {
        $model = new CartModel();

        self::assertFalse(isset($model->asdf));

        self::assertTrue(isset($model->items));
    }

    public function testInvalidPropertyGet(): void
    {
        $model = new CartModel();

        self::expectException(RuntimeException::class);

        self::expectExceptionMessage('Invalid property');

        $model->asdf;
    }

    public function testAsArray(): void
    {
        $user     = new UserModel();
        $user->id = 'foo-user-id';

        $model                = new CartModel();
        $model->id            = 'foo-id';
        $model->user          = $user;
        $model->totalItems    = 123;
        $model->totalQuantity = 456;

        self::assertSame(
            [
                'id' => 'foo-id',
                'user' =>
                    [
                        'id' => 'foo-user-id',
                        'isAdmin' => false,
                        'emailAddress' => '',
                        'isActive' => true,
                        'timezone' => 'US/Central',
                        'firstName' => '',
                        'lastName' => '',
                        'displayName' => '',
                        'billingName' => '',
                        'billingCompany' => '',
                        'billingPhone' => '',
                        'billingCountry' => '',
                        'billingAddress' => '',
                        'billingCity' => '',
                        'billingPostalCode' => '',
                        'createdAt' => $user->createdAt->format(
                            DateTimeInterface::ATOM
                        ),
                    ],
                'totalItems' => 123,
                'totalQuantity' => 456,
                'createdAt' => $model->createdAt->format(
                    DateTimeInterface::ATOM
                ),
            ],
            $model->asArray(false)
        );
    }

    public function testCalculateSubTotal(): void
    {
        $software1        = new SoftwareModel();
        $software1->price = 123.4;
        $item1            = new CartItemModel();
        $item1->software  = $software1;
        $item1->quantity  = 2;

        $software2        = new SoftwareModel();
        $software2->price = 34;
        $item2            = new CartItemModel();
        $item2->software  = $software2;
        $item2->quantity  = 3;

        $model = new CartModel();
        $model->addItem($item1);
        $model->addItem($item2);

        self::assertSame(348.8, $model->calculateSubTotal());

        $item2->quantity = 100;

        self::assertSame(3646.8, $model->calculateSubTotal());
    }

    public function testCalcTax(): void
    {
        $software1        = new SoftwareModel();
        $software1->price = 123.4;
        $item1            = new CartItemModel();
        $item1->software  = $software1;
        $item1->quantity  = 2;

        $software2        = new SoftwareModel();
        $software2->price = 34;
        $item2            = new CartItemModel();
        $item2->software  = $software2;
        $item2->quantity  = 3;

        $model = new CartModel();
        $model->addItem($item1);
        $model->addItem($item2);

        self::assertSame(0.0, $model->calculateTax());
        self::assertSame(348.8, $model->calculateTotal());

        $user                   = new UserModel();
        $user->billingStateAbbr = 'asdf';

        $model->user = $user;

        self::assertSame(0.0, $model->calculateTax());
        self::assertSame(348.8, $model->calculateTotal());

        $user->billingStateAbbr = 'TN';

        self::assertSame(24.42, $model->calculateTax());
        self::assertSame(373.22, $model->calculateTotal());
    }

    // TODO: Update this test
    // public function testCanPurchase() : void
    // {
    //     $user  = new UserModel();
    //     $model = new CartModel();
    //
    //     self::assertFalse($model->canPurchase());
    //
    //     $model->user = $user;
    //
    //     self::assertFalse($model->canPurchase());
    //
    //     $user->firstName = 'asdf';
    //     self::assertFalse($model->canPurchase());
    //
    //     $user->lastName = 'asdf';
    //     self::assertFalse($model->canPurchase());
    //
    //     $user->billingName = 'asdf';
    //     self::assertFalse($model->canPurchase());
    //
    //     $user->billingCountry = 'asdf';
    //     self::assertFalse($model->canPurchase());
    //
    //     $user->billingAddress = 'asdf';
    //     self::assertFalse($model->canPurchase());
    //
    //     $user->billingCity = 'asdf';
    //     self::assertFalse($model->canPurchase());
    //
    //     $user->billingPostalCode = 'asdf';
    //     self::assertFalse($model->canPurchase());
    //
    //     $user->billingStateAbbr = 'asdf';
    //     self::assertTrue($model->canPurchase());
    //
    //     $user->billingPostalCode = '';
    //     self::assertFalse($model->canPurchase());
    // }
}
