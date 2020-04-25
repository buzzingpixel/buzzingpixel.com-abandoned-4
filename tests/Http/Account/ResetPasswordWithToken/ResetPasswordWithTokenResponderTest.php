<?php

declare(strict_types=1);

namespace Tests\Http\Account\ResetPasswordWithToken;

use App\Content\Meta\MetaPayload;
use App\Http\Account\ResetPasswordWithToken\ResetPasswordWithTokenResponder;
use App\Users\Models\UserModel;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Tests\TestConfig;
use Throwable;
use Twig\Environment as TwigEnvironment;

class ResetPasswordWithTokenResponderTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $user = new UserModel();

        $token = 'bazToken';

        $twigEnvironment = $this->createMock(
            TwigEnvironment::class
        );

        $twigEnvironment->expects(self::once())
            ->method('render')
            ->with(
                self::equalTo('Account/ResetPassword.twig'),
                self::equalTo([
                    'metaPayload' => new MetaPayload(
                        ['metaTitle' => 'Reset your Password']
                    ),
                    'user' => $user,
                    'token' => $token,
                ]),
            )
            ->willReturn('fooTwigRender');

        $responder = new ResetPasswordWithTokenResponder(
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            ),
            $twigEnvironment
        );

        $response = $responder($user, $token);

        self::assertSame(
            'fooTwigRender',
            (string) $response->getBody(),
        );
    }
}
