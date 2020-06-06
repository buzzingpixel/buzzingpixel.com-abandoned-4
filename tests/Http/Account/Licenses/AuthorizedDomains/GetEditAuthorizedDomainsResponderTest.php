<?php

declare(strict_types=1);

namespace Tests\Http\Account\Licenses\AuthorizedDomains;

use App\Content\Meta\MetaPayload;
use App\Http\Account\Licenses\AuthorizedDomains\GetEditAuthorizedDomainsResponder;
use App\Licenses\Models\LicenseModel;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Tests\TestConfig;
use Throwable;
use Twig\Environment as TwigEnvironment;

class GetEditAuthorizedDomainsResponderTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $license = new LicenseModel();

        $twigEnv = $this->createMock(TwigEnvironment::class);

        $twigEnv->expects(self::once())
            ->method('render')
            ->with(
                self::equalTo(
                    'Http/Account/LicenseEditAuthorizedDomains.twig'
                ),
                self::equalTo(
                    [
                        'metaPayload' => new MetaPayload(
                            ['metaTitle' => 'Edit Authorized Domains on License']
                        ),
                        'activeTab' => 'licenses',
                        'breadcrumbs' => [
                            [
                                'href' => '/account/licenses',
                                'content' => 'All Licenses',
                            ],
                            [
                                'href' => '/account/licenses/view/' . $license->id,
                                'content' => 'License',
                            ],
                        ],
                        'license' => $license,
                    ]
                )
            )
            ->willReturn('twigReturnTest');

        $responder = new GetEditAuthorizedDomainsResponder(
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            ),
            $twigEnv,
        );

        $response = $responder($license);

        self::assertSame(
            'twigReturnTest',
            $response->getBody()->__toString()
        );
    }
}
