<?php

declare(strict_types=1);

namespace App\Cart\OrderProcessors;

use App\Cart\Models\ProcessOrderModel;
use App\Licenses\Models\LicenseModel;
use App\Orders\Models\OrderItemModel;
use App\Software\Models\SoftwareModel;
use DateInterval;

use function assert;

class PopulateOrderAfterCharge
{
    public function __invoke(
        ProcessOrderModel $processOrderModel
    ): ProcessOrderModel {
        $cart = $processOrderModel->cart();

        $order = $processOrderModel->order();

        $card = $processOrderModel->card();

        $stripeResponse = $processOrderModel->stripePaymentInfo();

        $order->stripeId = $stripeResponse->id;

        $order->stripeAmount = $stripeResponse->amount / 100;

        $order->stripeCaptured = true;

        $order->stripeCreated = $stripeResponse->created;

        $order->stripeCurrency = $stripeResponse->currency;

        $order->stripePaid = true;

        $order->subtotal = $cart->calculateSubTotal();

        $order->tax = $cart->calculateTax(
            $processOrderModel->card()->state
        );

        $order->total = $stripeResponse->amount / 100;

        $order->name = $card->nameOnCard;

        $order->company = $card->user->billingCompany;

        $order->phoneNumber = $card->user->billingPhone;

        $order->country = $card->country;

        $order->address = $card->address;

        $order->addressContinued = $card->address2;

        $order->city = $card->city;

        $order->state = $card->state;

        $order->postalCode = $card->postalCode;

        foreach ($cart->items as $cartItem) {
            $software = $cartItem->software;
            assert($software instanceof SoftwareModel);

            for ($i = 0; $i < $cartItem->quantity; $i++) {
                $license = new LicenseModel();

                $license->ownerUser = $order->user;

                $license->itemKey = $software->slug;

                $license->itemTitle = $software->name;

                $license->majorVersion = $software->versions[0]->majorVersion;

                $license->version = $software->versions[0]->version;

                $license->lastAvailableVersion = $software->versions[0]->version;

                // @codeCoverageIgnoreStart
                if ($software->isSubscription) {
                    $license->expires = $order->date->add(new DateInterval('P1Y'));
                }

                // @codeCoverageIgnoreEnd

                $orderItem = new OrderItemModel();

                $orderItem->order = $order;

                $orderItem->license = $license;

                $orderItem->itemKey = $software->slug;

                $orderItem->itemTitle = $software->name;

                $orderItem->majorVersion = $software->versions[0]->majorVersion;

                $orderItem->version = $software->versions[0]->version;

                $orderItem->price = $software->price;

                $orderItem->originalPrice = $software->price;

                $orderItem->isUpgrade = false;

                $orderItem->hasBeenUpgraded = false;

                $order->addItem($orderItem);
            }
        }

        return $processOrderModel;
    }
}
