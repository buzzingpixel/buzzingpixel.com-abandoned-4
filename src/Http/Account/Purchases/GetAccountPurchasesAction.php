<?php

declare(strict_types=1);

namespace App\Http\Account\Purchases;

use App\Orders\OrderApi;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetAccountPurchasesAction
{
    private GetAccountPurchasesResponder $responder;
    private OrderApi $orderApi;

    public function __construct(
        GetAccountPurchasesResponder $responder,
        OrderApi $orderApi
    ) {
        $this->responder = $responder;
        $this->orderApi  = $orderApi;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(): ResponseInterface
    {
        return ($this->responder)(
            $this->orderApi->fetchCurrentUserOrders()
        );
    }
}
