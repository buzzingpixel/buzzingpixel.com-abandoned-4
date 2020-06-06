<?php

declare(strict_types=1);

namespace Tests\Http\Account\Licenses;

use App\Content\Meta\MetaPayload;
use App\Http\Account\Licenses\GetAccountLicensesResponder;
use App\Licenses\Models\LicenseModel;
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
        $license = new LicenseModel();

        $license->id = 'foo-id';

        $license->itemTitle = 'Foo Title';

        $license->authorizedDomains[] = 'Foo Domain 1';

        $license->authorizedDomains[] = 'Foo Domain 2';

        $licenses = ['foo' => [$license]];

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
                        'heading' => 'Licenses',
                        'groups' => [
                            [
                                'title' => 'Foo Title',
                                'items' => [
                                    [
                                        'href' => '/account/licenses/view/foo-id',
                                        'title' => 'Foo Title',
                                        'subtitle' => 'foo-id',
                                        'column2' => [
                                            'Foo Domain 1',
                                            'Foo Domain 2',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ),
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
