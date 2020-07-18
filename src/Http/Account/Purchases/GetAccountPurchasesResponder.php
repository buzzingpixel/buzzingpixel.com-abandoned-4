<?php

declare(strict_types=1);

namespace App\Http\Account\Purchases;

use App\Content\Meta\MetaPayload;
use App\Orders\Models\OrderItemModel;
use App\Orders\Models\OrderModel;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Twig\Environment as TwigEnvironment;

use function array_map;
use function assert;
use function number_format;

class GetAccountPurchasesResponder
{
    private ResponseFactoryInterface $responseFactory;
    private TwigEnvironment $twigEnvironment;
    private UserApi $userApi;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        TwigEnvironment $twigEnvironment,
        UserApi $userApi
    ) {
        $this->responseFactory = $responseFactory;
        $this->twigEnvironment = $twigEnvironment;
        $this->userApi         = $userApi;
    }

    /**
     * @param OrderModel[] $orders
     *
     * @throws Throwable
     */
    public function __invoke(array $orders): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $user = $this->userApi->fetchLoggedInUser();

        assert($user instanceof UserModel);

        $purchases = array_map(
            static function (OrderModel $order) use ($user) {
                $orderItems = array_map(
                    static function (OrderItemModel $orderItem): string {
                        return $orderItem->itemTitle;
                    },
                    $order->items
                );

                return [
                    'href' => '/account/purchases/view/' . $order->id,
                    'title' => $order->date
                        ->setTimezone($user->timezone)
                        ->format('Y/m/d g:i a'),
                    'subtitle' => '$' . number_format(
                        $order->total,
                        2,
                        '.',
                        ','
                    ),
                    'column2' => $orderItems,
                ];
            },
            $orders
        );

        $response->getBody()->write($this->twigEnvironment->render(
            'Http/Account/Purchases.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Your Purchases']
                ),
                'activeTab' => 'purchases',
                'heading' => 'Purchases',
                'groups' => [['items' => $purchases]],
            ]
        ));

        return $response;
    }
}
