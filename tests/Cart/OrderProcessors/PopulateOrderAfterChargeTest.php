<?php

declare(strict_types=1);

namespace Tests\Cart\OrderProcessors;

use App\Cart\Models\CartItemModel;
use App\Cart\Models\CartModel;
use App\Cart\Models\ProcessOrderModel;
use App\Cart\OrderProcessors\PopulateOrderAfterCharge;
use App\Orders\Models\OrderModel;
use App\Software\Models\SoftwareModel;
use App\Software\Models\SoftwareVersionModel;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use PHPUnit\Framework\TestCase;
use Stripe\PaymentIntent;

use function time;

class PopulateOrderAfterChargeTest extends TestCase
{
    public function test(): void
    {
        $stripeResponse           = new PaymentIntent('foo-stripe-response-id');
        $stripeResponse->status   = 'SUCCEEDED';
        $stripeResponse->amount   = 123456;
        $stripeResponse->created  = time();
        $stripeResponse->currency = 'foo-stripe-currency';

        $user                 = new UserModel();
        $user->billingCompany = 'foo-user-billing-company';
        $user->billingPhone   = 'foo-user-billing-phone';

        $order       = new OrderModel();
        $order->id   = 'foo-order-id';
        $order->user = $user;

        $card             = new UserCardModel();
        $card->id         = 'foo-card-id';
        $card->stripeId   = 'foo-card-stripe-id';
        $card->user       = $user;
        $card->state      = 'foo-card-state';
        $card->nameOnCard = 'foo-card-name-on-card';
        $card->country    = 'USA';
        $card->address    = 'foo-card-address';
        $card->address2   = 'foo-card-address-cont';
        $card->city       = 'foo-card-city';
        $card->state      = 'TN';
        $card->postalCode = '37174';

        $softwareVersion1               = new SoftwareVersionModel();
        $softwareVersion1->id           = 'foo-software-version-1-id';
        $softwareVersion1->majorVersion = 'foo-major-version-1';
        $softwareVersion1->version      = 'foo-version-1';
        $softwareVersion2               = new SoftwareVersionModel();
        $softwareVersion2->id           = 'foo-software-version-2-id';
        $softwareVersion2->majorVersion = 'foo-major-version-2';
        $softwareVersion2->version      = 'foo-version-2';

        $software        = new SoftwareModel();
        $software->id    = 'foo-software-id';
        $software->slug  = 'foo-software-slug';
        $software->name  = 'Foo Software Name';
        $software->price = 1.43;
        $software->addVersion($softwareVersion1);
        $software->addVersion($softwareVersion2);

        $cartItem           = new CartItemModel();
        $cartItem->id       = 'foo-cart-item-id';
        $cartItem->software = $software;
        $cartItem->quantity = 2;

        $cart                = new CartModel();
        $cart->id            = 'foo-cart-id';
        $cart->user          = $user;
        $cart->totalItems    = 1;
        $cart->totalQuantity = 2;
        $cart->addItem($cartItem);

        $processOrderModel = new ProcessOrderModel(
            $cart,
            $card,
            $order
        );

        $processOrderModel->setStripePaymentInfo(
            $stripeResponse
        );

        $service = new PopulateOrderAfterCharge();

        self::assertSame(
            $processOrderModel,
            $service($processOrderModel),
        );

        self::assertSame(
            'foo-stripe-response-id',
            $order->stripeId,
        );

        self::assertSame(
            1234.56,
            $order->stripeAmount,
        );

        self::assertTrue($order->stripeCaptured);

        self::assertSame(
            $stripeResponse->created,
            $order->stripeCreated,
        );

        self::assertSame(
            $stripeResponse->currency,
            $order->stripeCurrency,
        );

        self::assertTrue($order->stripePaid);

        self::assertSame(
            2.86,
            $order->subtotal,
        );

        self::assertSame(
            0.2,
            $order->tax,
        );

        self::assertSame(
            1234.56,
            $order->total,
        );

        self::assertSame(
            'foo-card-name-on-card',
            $order->name,
        );

        self::assertSame(
            'foo-user-billing-company',
            $order->company,
        );

        self::assertSame(
            'foo-user-billing-phone',
            $order->phoneNumber,
        );

        self::assertSame(
            'USA',
            $order->country,
        );

        self::assertSame(
            'foo-card-address',
            $order->address,
        );

        self::assertSame(
            'foo-card-address-cont',
            $order->addressContinued,
        );

        self::assertSame(
            'foo-card-city',
            $order->city,
        );

        self::assertSame(
            'TN',
            $order->state,
        );

        self::assertSame(
            '37174',
            $order->postalCode,
        );

        self::assertCount(2, $order->items);

        $item1 = $order->items[0];

        self::assertSame(
            $order,
            $item1->order,
        );

        $license1 = $item1->license;

        self::assertSame(
            $user,
            $license1->ownerUser,
        );

        self::assertSame(
            'foo-software-slug',
            $license1->itemKey,
        );

        self::assertSame(
            'Foo Software Name',
            $license1->itemTitle,
        );

        self::assertSame(
            'foo-major-version-1',
            $license1->majorVersion,
        );

        self::assertSame(
            'foo-version-1',
            $license1->version,
        );

        self::assertSame(
            'foo-version-1',
            $license1->lastAvailableVersion,
        );

        self::assertSame(
            'foo-software-slug',
            $item1->itemKey,
        );

        self::assertSame(
            'Foo Software Name',
            $item1->itemTitle,
        );

        self::assertSame(
            'foo-major-version-1',
            $item1->majorVersion,
        );

        self::assertSame(
            'foo-version-1',
            $item1->version,
        );

        self::assertSame(
            1.43,
            $item1->price,
        );

        self::assertSame(
            1.43,
            $item1->originalPrice,
        );

        $item2 = $order->items[1];

        self::assertSame(
            $order,
            $item2->order,
        );

        $license2 = $item2->license;

        self::assertSame(
            $user,
            $license2->ownerUser,
        );

        self::assertSame(
            'foo-software-slug',
            $license2->itemKey,
        );

        self::assertSame(
            'Foo Software Name',
            $license2->itemTitle,
        );

        self::assertSame(
            'foo-major-version-1',
            $license2->majorVersion,
        );

        self::assertSame(
            'foo-version-1',
            $license2->version,
        );

        self::assertSame(
            'foo-version-1',
            $license2->lastAvailableVersion,
        );

        self::assertSame(
            'foo-software-slug',
            $item2->itemKey,
        );

        self::assertSame(
            'Foo Software Name',
            $item2->itemTitle,
        );

        self::assertSame(
            'foo-major-version-1',
            $item2->majorVersion,
        );

        self::assertSame(
            'foo-version-1',
            $item2->version,
        );

        self::assertSame(
            1.43,
            $item2->price,
        );

        self::assertSame(
            1.43,
            $item2->originalPrice,
        );
    }
}
