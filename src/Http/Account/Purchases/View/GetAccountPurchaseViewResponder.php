<?php

declare(strict_types=1);

namespace App\Http\Account\Purchases\View;

use App\Content\Meta\MetaPayload;
use App\Http\StandardResponderConstructor;
use App\Orders\Models\OrderModel;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetAccountPurchaseViewResponder extends StandardResponderConstructor
{
    /**
     * @throws Throwable
     */
    public function __invoke(OrderModel $order): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twigEnvironment->render(
            'Http/Account/PurchaseView.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Purchase']
                ),
                'activeTab' => 'purchases',
                'breadcrumbs' => [
                    [
                        'href' => '/account/purchases',
                        'content' => 'All Purchases',
                    ],
                ],
                'order' => $order,
            ]
        ));

        return $response;
    }
}
