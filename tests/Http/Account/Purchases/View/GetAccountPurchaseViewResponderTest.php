<?php

declare(strict_types=1);

namespace Tests\Http\Account\Purchases\View;

use App\Content\Meta\MetaPayload;
use App\Http\Account\Purchases\View\GetAccountPurchaseViewResponder;
use App\Orders\Models\OrderModel;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Tests\TestConfig;
use Throwable;
use Twig\Environment as TwigEnvironment;

class GetAccountPurchaseViewResponderTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $order = new OrderModel();

        $twigEnv = $this->createMock(TwigEnvironment::class);

        $twigEnv->expects(self::once())
            ->method('render')
            ->with(
                self::equalTo('Account/PurchaseView.twig'),
                self::equalTo(
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
                            ['content' => 'Purchase'],
                        ],
                        'order' => $order,
                    ]
                )
            )
            ->willReturn('twigReturnTest');

        $responder = new GetAccountPurchaseViewResponder(
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            ),
            $twigEnv,
        );

        $response = $responder($order);

        self::assertSame(
            'twigReturnTest',
            $response->getBody()->__toString()
        );
    }
}
