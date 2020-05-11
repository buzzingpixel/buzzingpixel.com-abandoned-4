<?php

declare(strict_types=1);

namespace Tests\Http\Account\Licenses;

use App\Content\Meta\MetaPayload;
use App\Http\Account\Licenses\GetAccountLicensesResponder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Tests\TestConfig;
use Throwable;
use Twig\Environment as TwigEnvironment;

class GetAccountLicensesResponderTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $licenses = ['test'];

        $twigEnv = $this->createMock(TwigEnvironment::class);

        $twigEnv->expects(self::once())
            ->method('render')
            ->with(
                self::equalTo('Http/Account/Licenses.twig'),
                self::equalTo(
                    [
                        'metaPayload' => new MetaPayload(
                            ['metaTitle' => 'Your Licenses']
                        ),
                        'activeTab' => 'licenses',
                        'licenses' => $licenses,
                    ]
                )
            )
            ->willReturn('twigReturnTest');

        $responder = new GetAccountLicensesResponder(
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            ),
            $twigEnv,
        );

        $response = $responder($licenses);

        self::assertSame(
            'twigReturnTest',
            $response->getBody()->__toString()
        );
    }
}
