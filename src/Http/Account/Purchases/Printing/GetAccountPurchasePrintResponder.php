<?php

declare(strict_types=1);

namespace App\Http\Account\Purchases\Printing;

use App\Http\StandardResponderConstructor;
use App\Orders\Models\OrderModel;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetAccountPurchasePrintResponder extends StandardResponderConstructor
{
    /**
     * @throws Throwable
     */
    public function __invoke(OrderModel $order) : ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twigEnvironment->render(
            'Account/PurchasePrintView.twig',
            ['order' => $order]
        ));

        return $response;
    }
}
