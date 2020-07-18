<?php

declare(strict_types=1);

namespace App\Cart\Services;

use App\Cart\Models\CartModel;
use App\Cart\Models\ProcessOrderModel;
use App\Cart\OrderProcessors\ChargeOrderToCard;
use App\Cart\OrderProcessors\ClearCartAtEnd;
use App\Cart\OrderProcessors\CreateSubscriptionsFromOrder;
use App\Cart\OrderProcessors\PopulateOrderAfterCharge;
use App\Cart\OrderProcessors\SaveOrderAfterPopulate;
use App\Orders\Models\OrderModel;
use App\Payload\Payload;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use App\Users\Models\UserCardModel;
use League\Pipeline\Pipeline;
use Psr\Container\ContainerInterface;
use Throwable;

use function assert;

class ProcessCartOrder
{
    private ContainerInterface $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function __invoke(CartModel $cart, UserCardModel $card): Payload
    {
        try {
            return $this->innerRun($cart, $card);
        } catch (Throwable $e) {
            return new Payload(Payload::STATUS_ERROR);
        }
    }

    /**
     * @throws Throwable
     */
    private function innerRun(CartModel $cart, UserCardModel $card): Payload
    {
        $uuidFactory = $this->di->get(UuidFactoryWithOrderedTimeCodec::class);
        assert($uuidFactory instanceof UuidFactoryWithOrderedTimeCodec);

        $order = new OrderModel();

        $order->user = $cart->user;

        $order->id = $uuidFactory->uuid1()->toString();

        $processOrderModel = new ProcessOrderModel(
            $cart,
            $card,
            $order,
        );

        $di = $this->di;

        $chargeOrderToCard = $di->get(ChargeOrderToCard::class);
        assert($chargeOrderToCard instanceof ChargeOrderToCard);

        $populateOrderAfterCharge = $di->get(PopulateOrderAfterCharge::class);
        assert($populateOrderAfterCharge instanceof PopulateOrderAfterCharge);

        $saveOrderAfterPopulate = $di->get(SaveOrderAfterPopulate::class);
        assert($saveOrderAfterPopulate instanceof SaveOrderAfterPopulate);

        $createSubscriptionsFromOrder = $di->get(CreateSubscriptionsFromOrder::class);
        assert($createSubscriptionsFromOrder instanceof CreateSubscriptionsFromOrder);

        $clearCartAtEnd = $di->get(ClearCartAtEnd::class);
        assert($clearCartAtEnd instanceof ClearCartAtEnd);

        $pipeline = (new Pipeline())
            ->pipe($chargeOrderToCard)
            ->pipe($populateOrderAfterCharge)
            ->pipe($saveOrderAfterPopulate)
            ->pipe($createSubscriptionsFromOrder)
            ->pipe($clearCartAtEnd);

        $pipeline->process($processOrderModel);

        return new Payload(
            Payload::STATUS_SUCCESSFUL,
            ['orderId' => $order->id]
        );
    }
}
