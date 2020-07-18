<?php

declare(strict_types=1);

namespace Tests\Http\Account\PaymentMethods\Create;

use App\Content\Meta\MetaPayload;
use App\Http\Account\PaymentMethods\Create\GetCreatePaymentMethodAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Tests\TestConfig;
use Throwable;
use Twig\Environment as TwigEnvironment;

class GetCreatePaymentMethodActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $twigEnv = $this->createMock(TwigEnvironment::class);

        $twigEnv->expects(self::once())
            ->method('render')
            ->with(
                self::equalTo('Http/Account/PaymentMethodCreate.twig'),
                self::equalTo(
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
                )
            )
            ->willReturn('twigReturnTest');

        $responder = new GetCreatePaymentMethodAction(
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            ),
            $twigEnv,
        );

        $response = $responder();

        self::assertSame(
            'twigReturnTest',
            $response->getBody()->__toString()
        );
    }
}
