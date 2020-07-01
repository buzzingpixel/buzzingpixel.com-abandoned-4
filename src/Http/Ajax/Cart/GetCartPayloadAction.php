<?php

declare(strict_types=1);

namespace App\Http\Ajax\Cart;

use App\Cart\CartApi;
use App\Users\UserApi;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

use function number_format;
use function Safe\json_encode;

class GetCartPayloadAction
{
    private CartApi $cartApi;
    private UserApi $userApi;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        CartApi $cartApi,
        UserApi $userApi,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->cartApi         = $cartApi;
        $this->userApi         = $userApi;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $user = $this->userApi->fetchLoggedInUser();

        $params = $request->getQueryParams();

        $paymentMethodId = (string) ($params['selected_payment_method'] ?? '');

        $paymentMethodState = '';

        if ($user !== null && $paymentMethodId !== '') {
            $paymentMethod = $this->userApi->fetchUserCardById(
                $user,
                $paymentMethodId
            );

            $paymentMethodState = $paymentMethod->state ?? '';
        }

        $currentUserCart = $this->cartApi->fetchCurrentUserCart();

        $response = $this->responseFactory->createResponse()
            ->withHeader(
                'Content-type',
                'application/json'
            );

        $response->getBody()->write(json_encode(
            [
                'totalQuantity' => $currentUserCart->totalQuantity,
                'subTotal' => '$' . number_format(
                    $currentUserCart->calculateSubTotal(),
                    2
                ),
                'tax' => '$' . number_format(
                    $currentUserCart->calculateTax(
                        $paymentMethodState
                    ),
                    2
                ),
                'total' => '$' . number_format(
                    $currentUserCart->calculateTotal(
                        $paymentMethodState
                    ),
                    2
                ),
            ]
        ));

        return $response;
    }
}
