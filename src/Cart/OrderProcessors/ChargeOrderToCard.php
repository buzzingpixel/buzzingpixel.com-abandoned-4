<?php

declare(strict_types=1);

namespace App\Cart\OrderProcessors;

use App\Cart\Models\ProcessOrderModel;
use App\Software\Models\SoftwareModel;
use App\Users\Models\UserModel;
use Exception;
use Stripe\StripeClient;
use Throwable;

use function assert;
use function mb_strtolower;

class ChargeOrderToCard
{
    private StripeClient $stripeClient;

    public function __construct(
        StripeClient $stripeClient
    ) {
        $this->stripeClient = $stripeClient;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(
        ProcessOrderModel $processOrderModel
    ): ProcessOrderModel {
        $cart = $processOrderModel->cart();

        $order = $processOrderModel->order();

        $card = $processOrderModel->card();

        $user = $cart->user;
        assert($user instanceof UserModel);

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

        $stripeResponse = $this->stripeClient->paymentIntents->create([
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
        ]);

        $processOrderModel->setStripePaymentInfo(
            $stripeResponse
        );

        if (mb_strtolower($stripeResponse->status) !== 'succeeded') {
            throw new Exception('Something went wrong with the payment');
        }

        return $processOrderModel;
    }
}
