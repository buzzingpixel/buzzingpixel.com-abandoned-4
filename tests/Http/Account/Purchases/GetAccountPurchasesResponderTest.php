<?php

declare(strict_types=1);

namespace Tests\Http\Account\Purchases;

use App\Content\Meta\MetaPayload;
use App\Http\Account\Purchases\GetAccountPurchasesResponder;
use App\Orders\Models\OrderModel;
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
        $orders = [new OrderModel()];

        $twigEnv = $this->createMock(TwigEnvironment::class);

        $twigEnv->expects(self::once())
            ->method('render')
            ->with(
                self::equalTo('Account/Purchases.twig'),
                self::equalTo(
                    [
                        'metaPayload' => new MetaPayload(
                            ['metaTitle' => 'Your Purchases']
                        ),
                        'activeTab' => 'purchases',
                        'orders' => $orders,
                    ]
                )
            )
            ->willReturn('twigReturnTest');

        $responder = new GetAccountPurchasesResponder(
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            ),
            $twigEnv,
        );

        $response = $responder($orders);

        self::assertSame(
            'twigReturnTest',
            $response->getBody()->__toString()
        );
    }
}
