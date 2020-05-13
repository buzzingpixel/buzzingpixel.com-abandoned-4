<?php

declare(strict_types=1);

namespace App\Http\Account\Purchases;

use App\Content\Meta\MetaPayload;
use App\Http\StandardResponderConstructor;
use App\Orders\Models\OrderModel;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetAccountPurchasesResponder extends StandardResponderConstructor
{
    /**
     * @param OrderModel[] $orders
     *
     * @throws Throwable
     */
    public function __invoke(array $orders) : ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twigEnvironment->render(
            'Http/Account/Purchases.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Your Purchases']
                ),
                'activeTab' => 'purchases',
                'orders' => $orders,
            ]
        ));

        return $response;
    }
}
