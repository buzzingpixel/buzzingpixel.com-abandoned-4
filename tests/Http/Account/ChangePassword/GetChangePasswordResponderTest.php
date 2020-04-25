<?php

declare(strict_types=1);

namespace Tests\Http\Account\ChangePassword;

use App\Content\Meta\MetaPayload;
use App\Http\Account\ChangePassword\GetChangePasswordResponder;
use App\Users\Models\UserModel;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Tests\TestConfig;
use Throwable;
use Twig\Environment as TwigEnvironment;

class GetChangePasswordResponderTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $user = new UserModel();

        $twigEnv = $this->createMock(TwigEnvironment::class);

        $twigEnv->expects(self::once())
            ->method('render')
            ->with(
                self::equalTo('Account/ChangePassword.twig'),
                self::equalTo(
                    [
                        'metaPayload' => new MetaPayload(
                            ['metaTitle' => 'Change Your Password']
                        ),
                        'activeTab' => 'change-password',
                        'user' => $user,
                    ]
                )
            )
            ->willReturn('twigReturnTest');

        $responder = new GetChangePasswordResponder(
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            ),
            $twigEnv,
        );

        $response = $responder($user);

        self::assertSame(
            'twigReturnTest',
            $response->getBody()->__toString()
        );
    }
}
