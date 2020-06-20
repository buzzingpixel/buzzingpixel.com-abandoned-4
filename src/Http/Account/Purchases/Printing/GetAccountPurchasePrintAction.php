<?php

declare(strict_types=1);

namespace App\Http\Account\Purchases\Printing;

use App\Orders\OrderApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;

class GetAccountPurchasePrintAction
{
    private GetAccountPurchasePrintResponder $responder;
    private OrderApi $orderApi;

    public function __construct(
        GetAccountPurchasePrintResponder $responder,
        OrderApi $orderApi
    ) {
        $this->responder = $responder;
        $this->orderApi  = $orderApi;
    }

    /**
     * @throws Throwable
     * @throws HttpNotFoundException
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $order = $this->orderApi->fetchCurrentUserOrderById(
            (string) $request->getAttribute('id')
        );

        if ($order === null) {
            throw new HttpNotFoundException($request);
        }

        return ($this->responder)($order);
    }
}
