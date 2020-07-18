<?php

declare(strict_types=1);

namespace Tests\Cart\OrderProcessors;

use App\Cart\Models\CartModel;
use App\Cart\Models\ProcessOrderModel;
use App\Cart\OrderProcessors\CreateSubscriptionsFromOrder;
use App\Licenses\Models\LicenseModel;
use App\Orders\Models\OrderItemModel;
use App\Orders\Models\OrderModel;
use App\Payload\Payload;
use App\Software\Models\SoftwareModel;
use App\Software\SoftwareApi;
use App\Subscriptions\Models\SubscriptionModel;
use App\Subscriptions\SubscriptionApi;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use PHPUnit\Framework\TestCase;

class CreateSubscriptionsFromOrderTest extends TestCase
{
    public function test(): void
    {
        $ansel                 = new SoftwareModel();
        $ansel->slug           = 'ansel-ee';
        $ansel->isSubscription = true;

        $craft                 = new SoftwareModel();
        $craft->slug           = 'ansel-craft';
        $craft->isSubscription = false;

        $license1            = new LicenseModel();
        $orderItem1          = new OrderItemModel();
        $orderItem1->itemKey = 'ansel-ee';
        $orderItem1->license = $license1;

        $license2            = new LicenseModel();
        $orderItem2          = new OrderItemModel();
        $orderItem2->itemKey = 'ansel-craft';
        $orderItem2->license = $license2;

        $order     = new OrderModel();
        $order->id = 'foo-order-id';
        $order->addItem($orderItem1);
        $order->addItem($orderItem2);

        $user = new UserModel();

        $cart       = new CartModel();
        $cart->user = $user;

        $card = new UserCardModel();

        $subscriptionApi = $this->createMock(
            SubscriptionApi::class,
        );

        $subscriptionApi->expects(self::once())
            ->method('saveSubscription')
            ->willReturnCallback(static function (
                SubscriptionModel $subscription
            ) use (
                $user,
                $license1,
                $card,
                $order
            ): Payload {
                self::assertSame('', $subscription->id);

                self::assertSame($user, $subscription->user);

                self::assertSame(
                    $license1,
                    $subscription->license
                );

                self::assertSame(
                    [$order],
                    $subscription->orders,
                );

                self::assertTrue($subscription->autoRenew);

                self::assertSame($card, $subscription->card);

                return new Payload(Payload::STATUS_CREATED);
            });

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->method('fetchAllSoftware')
            ->willReturn([$ansel, $craft]);

        $processOrderModel = new ProcessOrderModel(
            $cart,
            $card,
            $order,
        );

        $service = new CreateSubscriptionsFromOrder(
            $subscriptionApi,
            $softwareApi,
        );

        self::assertSame(
            $processOrderModel,
            $service($processOrderModel),
        );
    }
}
