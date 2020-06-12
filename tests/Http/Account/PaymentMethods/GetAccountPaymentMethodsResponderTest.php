<?php

declare(strict_types=1);

namespace Tests\Http\Account\PaymentMethods;

use App\Content\Meta\MetaPayload;
use App\Http\Account\PaymentMethods\GetAccountPaymentMethodsResponder;
use App\Users\Models\UserCardModel;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Tests\TestConfig;
use Throwable;
use Twig\Environment as TwigEnvironment;

class GetAccountPaymentMethodsResponderTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $userCards = [new UserCardModel()];

        $twigEnv = $this->createMock(TwigEnvironment::class);

        $twigEnv->expects(self::once())
            ->method('render')
            ->with(
                self::equalTo('Http/Account/PaymentMethods.twig'),
                self::equalTo(
                    [
                        'metaPayload' => new MetaPayload(
                            ['metaTitle' => 'Your Payment Methods']
                        ),
                        'activeTab' => 'payment-methods',
                        'userCards' => $userCards,
                    ]
                )
            )
            ->willReturn('twigReturnTest');

        $responder = new GetAccountPaymentMethodsResponder(
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            ),
            $twigEnv,
        );

        $response = $responder($userCards);

        self::assertSame(
            'twigReturnTest',
            $response->getBody()->__toString()
        );
    }
}
