<?php

declare(strict_types=1);

namespace Tests\Http\Account\Purchases\Printing;

use App\Http\Account\Purchases\Printing\GetAccountPurchasePrintResponder;
use App\Orders\Models\OrderModel;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Tests\TestConfig;
use Throwable;
use Twig\Environment as TwigEnvironment;

class GetAccountPurchasePrintResponderTest extends TestCase
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
                self::equalTo(
                    'Http/Account/PurchasePrintView.twig'
                ),
                self::equalTo(
                    ['order' => $order]
                )
            )
            ->willReturn('twigReturnTest');

        $responder = new GetAccountPurchasePrintResponder(
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
