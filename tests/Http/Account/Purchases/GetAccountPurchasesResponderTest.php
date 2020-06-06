<?php

declare(strict_types=1);

namespace Tests\Http\Account\Purchases;

use App\Content\Meta\MetaPayload;
use App\Http\Account\Purchases\GetAccountPurchasesResponder;
use App\Orders\Models\OrderItemModel;
use App\Orders\Models\OrderModel;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Tests\TestConfig;
use Throwable;
use Twig\Environment as TwigEnvironment;

class GetAccountPurchasesResponderTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $userModel = new UserModel();

        $orderItem = new OrderItemModel();

        $orderItem->itemTitle = 'Foo Bar Title';

        $order = new OrderModel();

        $order->total = 7.0;

        $order->addItem($orderItem);

        $order->id = 'foo-bar';

        $orders = [$order];

        $twigEnv = $this->createMock(TwigEnvironment::class);

        $twigEnv->expects(self::once())
            ->method('render')
            ->with(
                self::equalTo('Http/Account/Purchases.twig'),
                self::equalTo(
                    [
                        'metaPayload' => new MetaPayload(
                            ['metaTitle' => 'Your Purchases']
                        ),
                        'activeTab' => 'purchases',
                        'heading' => 'Purchases',
                        'groups' => [
                            [
                                'items' => [
                                    [
                                        'href' => '/account/purchases/view/foo-bar',
                                        'title' => $order->date
                                            ->setTimezone($userModel->timezone)
                                            ->format('Y/m/d g:i a'),
                                        'subtitle' => '$7.00',
                                        'column2' => ['Foo Bar Title'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ),
            )
            ->willReturn('twigReturnTest');

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($userModel);

        $responder = new GetAccountPurchasesResponder(
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            ),
            $twigEnv,
            $userApi,
        );

        $response = $responder($orders);

        self::assertSame(
            'twigReturnTest',
            $response->getBody()->__toString()
        );
    }
}
