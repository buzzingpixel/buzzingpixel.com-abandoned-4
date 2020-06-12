<?php

declare(strict_types=1);

namespace App\Http\Account\PaymentMethods;

use App\Content\Meta\MetaPayload;
use App\Http\StandardResponderConstructor;
use App\Users\Models\UserCardModel;
use Psr\Http\Message\ResponseInterface;
use ReflectionException;
use Throwable;

class GetAccountPaymentMethodsResponder extends StandardResponderConstructor
{
    /**
     * @param UserCardModel[] $userCards
     *
     * @throws ReflectionException
     * @throws Throwable
     */
    public function __invoke(array $userCards) : ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twigEnvironment->render(
            'Http/Account/PaymentMethods.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Your Payment Methods']
                ),
                'activeTab' => 'payment-methods',
                'userCards' => $userCards,
            ]
        ));

        return $response;
    }
}
