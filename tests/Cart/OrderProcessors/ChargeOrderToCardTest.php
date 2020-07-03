<?php

declare(strict_types=1);

namespace Tests\Cart\OrderProcessors;

use App\Cart\Models\CartItemModel;
use App\Cart\Models\CartModel;
use App\Cart\Models\ProcessOrderModel;
use App\Cart\OrderProcessors\ChargeOrderToCard;
use App\Orders\Models\OrderModel;
use App\Software\Models\SoftwareModel;
use App\Software\Models\SoftwareVersionModel;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use PHPUnit\Framework\TestCase;
use Stripe\PaymentIntent;
use Stripe\Service\PaymentIntentService;
use Stripe\StripeClient;
use Throwable;

use function assert;

class ChargeOrderToCardTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testWhenNotSucceeded(): void
    {
        $stripeResponse         = new PaymentIntent();
        $stripeResponse->status = 'failed';

        $user = new UserModel();

        $order       = new OrderModel();
        $order->id   = 'foo-order-id';
        $order->user = $user;

        $card           = new UserCardModel();
        $card->id       = 'foo-card-id';
        $card->stripeId = 'foo-card-stripe-id';
        $card->user     = $user;
        $card->state    = 'foo-card-state';

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

        $metaData = [
            'app_order_id' => $cart->id,
            'app_user_id' => $user->id,
            'total_items' => $cart->totalItems,
            'total_quantity' => $cart->totalQuantity,
            'items' => [],
        ];

        foreach ($cart->items as $item) {
            $software = $item->software;
            assert($software instanceof SoftwareModel);

            $key = 'item_' . $software->slug;

            $metaData[$key . '_quantity'] = (string) $item->quantity;

            $metaData[$key . '_app_software_id'] = $software->id;

            $metaData[$key . '_app_software_slug'] = $software->slug;

            $metaData[$key . '_app_software_name'] = $software->name;

            $metaData[$key . '_app_software_price'] = (string) $software->price;
        }

        $totalToChargeFloat = $cart->calculateTotal(
            $card->state
        );

        $totalToChargeCents = (int) ($totalToChargeFloat * 100);

        $paymentIntentService = $this->createMock(
            PaymentIntentService::class
        );

        $paymentIntentService->expects(self::once())
            ->method('create')
            ->with(self::equalTo([
                'amount' => $totalToChargeCents,
                'currency' => 'usd',
                'confirm' => true,
                'error_on_requires_action' => true,
                'customer' => $user->stripeId,
                'description' => 'Order ID: ' . $order->id,
                'metadata' => $metaData,
                'off_session' => true,
                'payment_method' => $card->stripeId,
                'payment_method_types' => ['card'],
            ]))
            ->willReturn($stripeResponse);

        $stripeClient = $this->createMock(
            StripeClient::class
        );

        $stripeClient->expects(self::once())
            ->method('__get')
            ->with(self::equalTo('paymentIntents'))
            ->willReturn($paymentIntentService);

        $service = new ChargeOrderToCard($stripeClient);

        $exception = null;

        try {
            $service($processOrderModel);
        } catch (Throwable $e) {
            $exception = $e;
        }

        self::assertInstanceOf(Throwable::class, $exception);

        self::assertSame(
            $stripeResponse,
            $processOrderModel->stripePaymentInfo(),
        );
    }

    /**
     * @throws Throwable
     */
    public function test(): void
    {
        $stripeResponse         = new PaymentIntent();
        $stripeResponse->status = 'SUCCEEDED';

        $user = new UserModel();

        $order       = new OrderModel();
        $order->id   = 'foo-order-id';
        $order->user = $user;

        $card           = new UserCardModel();
        $card->id       = 'foo-card-id';
        $card->stripeId = 'foo-card-stripe-id';
        $card->user     = $user;
        $card->state    = 'foo-card-state';

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

        $metaData = [
            'app_order_id' => $cart->id,
            'app_user_id' => $user->id,
            'total_items' => $cart->totalItems,
            'total_quantity' => $cart->totalQuantity,
            'items' => [],
        ];

        foreach ($cart->items as $item) {
            $software = $item->software;
            assert($software instanceof SoftwareModel);

            $key = 'item_' . $software->slug;

            $metaData[$key . '_quantity'] = (string) $item->quantity;

            $metaData[$key . '_app_software_id'] = $software->id;

            $metaData[$key . '_app_software_slug'] = $software->slug;

            $metaData[$key . '_app_software_name'] = $software->name;

            $metaData[$key . '_app_software_price'] = (string) $software->price;
        }

        $totalToChargeFloat = $cart->calculateTotal(
            $card->state
        );

        $totalToChargeCents = (int) ($totalToChargeFloat * 100);

        $paymentIntentService = $this->createMock(
            PaymentIntentService::class
        );

        $paymentIntentService->expects(self::once())
            ->method('create')
            ->with(self::equalTo([
                'amount' => $totalToChargeCents,
                'currency' => 'usd',
                'confirm' => true,
                'error_on_requires_action' => true,
                'customer' => $user->stripeId,
                'description' => 'Order ID: ' . $order->id,
                'metadata' => $metaData,
                'off_session' => true,
                'payment_method' => $card->stripeId,
                'payment_method_types' => ['card'],
            ]))
            ->willReturn($stripeResponse);

        $stripeClient = $this->createMock(
            StripeClient::class
        );

        $stripeClient->expects(self::once())
            ->method('__get')
            ->with(self::equalTo('paymentIntents'))
            ->willReturn($paymentIntentService);

        $service = new ChargeOrderToCard($stripeClient);

        self::assertSame(
            $processOrderModel,
            $service($processOrderModel),
        );

        self::assertSame(
            $stripeResponse,
            $processOrderModel->stripePaymentInfo(),
        );
    }
}
