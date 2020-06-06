<?php

declare(strict_types=1);

namespace Tests\Http\Account\Profile;

use App\Content\Meta\MetaPayload;
use App\Http\Account\Profile\GetAccountProfileResponder;
use App\Users\Models\UserModel;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Tests\TestConfig;
use Throwable;
use Twig\Environment as TwigEnvironment;

class GetAccountProfileResponderTest extends TestCase
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
                self::equalTo('Http/Account/ProfileView.twig'),
                self::equalTo(
                    [
                        'metaPayload' => new MetaPayload(
                            ['metaTitle' => 'Edit Your Profile']
                        ),
                        'activeTab' => 'profile',
                        'user' => $user,
                    ]
                )
            )
            ->willReturn('twigReturnTest');

        $responder = new GetAccountProfileResponder(
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
