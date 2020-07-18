<?php

declare(strict_types=1);

namespace Tests\Http\Account\Licenses\View;

use App\Content\Meta\MetaPayload;
use App\Http\Account\Licenses\View\GetAccountLicenseViewResponder;
use App\Licenses\LicenseApi;
use App\Licenses\Models\LicenseModel;
use App\Licenses\Services\LicenseStatus;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Tests\TestConfig;
use Throwable;
use Twig\Environment as TwigEnvironment;

class GetAccountLicenseViewResponderTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test(): void
    {
        $license = new LicenseModel();

        $licenseStatus = $this->createMock(
            LicenseStatus::class
        );

        $licenseStatus->expects(self::once())
            ->method('statusString')
            ->with(self::equalTo($license))
            ->willReturn('foo-status-string');

        $licenseApi = $this->createMock(LicenseApi::class);

        $licenseApi->method('licenseStatus')
            ->willReturn($licenseStatus);

        $twigEnv = $this->createMock(TwigEnvironment::class);

        $twigEnv->expects(self::once())
            ->method('render')
            ->with(
                self::equalTo(
                    'Http/Account/LicenseView.twig'
                ),
                self::equalTo(
                    [
                        'metaPayload' => new MetaPayload(
                            ['metaTitle' => 'License']
                        ),
                        'activeTab' => 'licenses',
                        'breadcrumbs' => [
                            [
                                'href' => '/account/licenses',
                                'content' => 'All Licenses',
                            ],
                        ],
                        'license' => $license,
                        'statusString' => 'Foo-status-string',
                    ]
                )
            )
            ->willReturn('twigReturnTest');

        $responder = new GetAccountLicenseViewResponder(
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            ),
            $twigEnv,
            $licenseApi,
        );

        $response = $responder($license);

        self::assertSame(
            'twigReturnTest',
            $response->getBody()->__toString()
        );
    }
}
