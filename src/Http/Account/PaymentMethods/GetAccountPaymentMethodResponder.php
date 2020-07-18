<?php

declare(strict_types=1);

namespace App\Http\Account\PaymentMethods;

use App\Content\Meta\MetaPayload;
use App\Http\StandardResponderConstructor;
use App\Users\Models\UserCardModel;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetAccountPaymentMethodResponder extends StandardResponderConstructor
{
    /**
     * @throws Throwable
     */
    public function __invoke(UserCardModel $userCard): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twigEnvironment->render(
            'Http/Account/PaymentMethod.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Payment Method']
                ),
                'activeTab' => 'payment-methods',
                'breadcrumbs' => [
                    [
                        'href' => '/account/payment-methods',
                        'content' => 'Payment Methods',
                    ],
                ],
                'userCard' => $userCard,
            ]
        ));

        return $response;
    }
}
