<?php

declare(strict_types=1);

namespace App\Cart\Models;

use App\Orders\Models\OrderModel;
use App\Users\Models\UserCardModel;
use Stripe\PaymentIntent;

class ProcessOrderModel
{
    private CartModel $cart;
    private UserCardModel $card;
    private OrderModel $orderModel;
    /** @psalm-suppress PropertyNotSetInConstructor */
    private PaymentIntent $stripePaymentInfo;

    public function __construct(
        CartModel $cart,
        UserCardModel $card,
        OrderModel $orderModel
    ) {
        $this->cart       = $cart;
        $this->card       = $card;
        $this->orderModel = $orderModel;
    }

    public function cart(): CartModel
    {
        return $this->cart;
    }

    public function card(): UserCardModel
    {
        return $this->card;
    }

    public function order(): OrderModel
    {
        return $this->orderModel;
    }

    public function setStripePaymentInfo(PaymentIntent $stripePaymentInfo): void
    {
        $this->stripePaymentInfo = $stripePaymentInfo;
    }

    public function stripePaymentInfo(): PaymentIntent
    {
        return $this->stripePaymentInfo;
    }
}
