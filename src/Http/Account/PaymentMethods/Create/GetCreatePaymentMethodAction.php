<?php

declare(strict_types=1);

namespace App\Http\Account\PaymentMethods\Create;

use App\Content\Meta\MetaPayload;
use App\Http\StandardResponderConstructor;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetCreatePaymentMethodAction extends StandardResponderConstructor
{
    /**
     * @throws Throwable
     */
    public function __invoke() : ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twigEnvironment->render(
            'Http/Account/PaymentMethodCreate.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Create Payment Method']
                ),
                'activeTab' => 'payment-methods',
                'breadcrumbs' => [
                    [
                        'href' => '/account/payment-methods',
                        'content' => 'Payment Methods',
                    ],
                ],
            ]
        ));

        return $response;
    }
}
