<?php

declare(strict_types=1);

namespace Tests\Http\Account\PaymentMethods;

use App\Content\Meta\MetaPayload;
use App\Http\Account\PaymentMethods\GetAccountPaymentMethodResponder;
use App\Users\Models\UserCardModel;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Tests\TestConfig;
use Throwable;
use Twig\Environment as TwigEnvironment;

class GetAccountPaymentMethodResponderTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $userCard = new UserCardModel();

        $twigEnv = $this->createMock(TwigEnvironment::class);

        $twigEnv->expects(self::once())
            ->method('render')
            ->with(
                self::equalTo('Http/Account/PaymentMethod.twig'),
                self::equalTo(
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
                )
            )
            ->willReturn('twigReturnTest');

        $responder = new GetAccountPaymentMethodResponder(
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            ),
            $twigEnv,
        );

        $response = $responder($userCard);

        self::assertSame(
            'twigReturnTest',
            $response->getBody()->__toString()
        );
    }
}
