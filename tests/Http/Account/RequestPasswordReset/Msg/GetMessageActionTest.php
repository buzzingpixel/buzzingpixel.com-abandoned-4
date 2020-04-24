<?php

declare(strict_types=1);

namespace Tests\Http\Account\RequestPasswordReset\Msg;

use App\Content\Meta\MetaPayload;
use App\Http\Account\RequestPasswordReset\Msg\GetMessageAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Tests\TestConfig;
use Throwable;
use Twig\Environment as TwigEnvironment;

class GetMessageActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $twigEnvironment = $this->createMock(
            TwigEnvironment::class
        );

        $twigEnvironment->expects(self::once())
            ->method('render')
            ->with(
                self::equalTo('MessageOnly.twig'),
                self::equalTo([
                    'metaPayload' => new MetaPayload(
                        ['metaTitle' => 'Password Reset']
                    ),
                    'message' => 'If that email address is associated with an' .
                        " account and you haven't requested a reset more than 5" .
                        " times in the last 2 hours, we'll send password reset" .
                        ' instructions to that email address.',
                ]),
            )
            ->willReturn('twigRender');

        $action = new GetMessageAction(
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            ),
            $twigEnvironment,
        );

        $response = $action();

        self::assertSame(
            'twigRender',
            (string) $response->getBody(),
        );
    }
}
