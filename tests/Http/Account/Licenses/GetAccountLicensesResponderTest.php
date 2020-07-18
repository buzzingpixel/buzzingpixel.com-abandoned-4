<?php

declare(strict_types=1);

namespace Tests\Http\Account\Licenses;

use App\Content\Meta\MetaPayload;
use App\Http\Account\Licenses\GetAccountLicensesResponder;
use App\Licenses\LicenseApi;
use App\Licenses\Models\LicenseModel;
use App\Licenses\Services\LicenseStatus;
use App\Utilities\SystemClock;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Safe\DateTimeImmutable;
use Tests\TestConfig;
use Throwable;
use Twig\Environment as TwigEnvironment;

use function strtotime;

class GetAccountLicensesResponderTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test(): void
    {
        $currentTime = new DateTimeImmutable();

        $clock = $this->createMock(SystemClock::class);

        $clock->method('getCurrentTime')
            ->willReturn($currentTime);

        $licenseStatus = new LicenseStatus($clock);

        $licenseApi = $this->createMock(LicenseApi::class);

        $licenseApi->expects(self::once())
            ->method('licenseStatus')
            ->willReturn($licenseStatus);

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
                                        'title' => 'Foo Title (active)',
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
            $licenseApi,
        );

        $response = $responder($licenses);

        self::assertSame(
            'twigReturnTest',
            $response->getBody()->__toString()
        );
    }

    /**
     * @throws Throwable
     */
    public function testExpiresMoreThanOne7days(): void
    {
        $currentTime = new DateTimeImmutable();

        $clock = $this->createMock(SystemClock::class);

        $clock->method('getCurrentTime')
            ->willReturn($currentTime);

        $licenseStatus = new LicenseStatus($clock);

        $licenseApi = $this->createMock(LicenseApi::class);

        $licenseApi->expects(self::once())
            ->method('licenseStatus')
            ->willReturn($licenseStatus);

        $expires = new DateTimeImmutable();

        $expires = $expires->setTimestamp(
            strtotime('+9 days')
        );

        $license = new LicenseModel();

        $license->expires = $expires;

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
                                        'title' => 'Foo Title (active)',
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
            $licenseApi,
        );

        $response = $responder($licenses);

        self::assertSame(
            'twigReturnTest',
            $response->getBody()->__toString()
        );
    }

    /**
     * @throws Throwable
     */
    public function testExpiresIn5Days(): void
    {
        $currentTime = new DateTimeImmutable();

        $clock = $this->createMock(SystemClock::class);

        $clock->method('getCurrentTime')
            ->willReturn($currentTime);

        $licenseStatus = new LicenseStatus($clock);

        $licenseApi = $this->createMock(LicenseApi::class);

        $licenseApi->expects(self::once())
            ->method('licenseStatus')
            ->willReturn($licenseStatus);

        $expires = new DateTimeImmutable();

        $expires = $expires->setTimestamp(
            strtotime('+5 days')
        );

        $license = new LicenseModel();

        $license->expires = $expires;

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
                                        'title' => 'Foo Title (expires in 5 days)',
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
            $licenseApi,
        );

        $response = $responder($licenses);

        self::assertSame(
            'twigReturnTest',
            $response->getBody()->__toString()
        );
    }

    /**
     * @throws Throwable
     */
    public function testExpiresToday(): void
    {
        $currentTime = new DateTimeImmutable();

        $clock = $this->createMock(SystemClock::class);

        $clock->method('getCurrentTime')
            ->willReturn($currentTime);

        $licenseStatus = new LicenseStatus($clock);

        $licenseApi = $this->createMock(LicenseApi::class);

        $licenseApi->expects(self::once())
            ->method('licenseStatus')
            ->willReturn($licenseStatus);

        $expires = new DateTimeImmutable();

        $expires = $expires->setTimestamp(
            strtotime('+30 minutes')
        );

        $license = new LicenseModel();

        $license->expires = $expires;

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
                                        'title' => 'Foo Title (expires today!)',
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
            $licenseApi,
        );

        $response = $responder($licenses);

        self::assertSame(
            'twigReturnTest',
            $response->getBody()->__toString()
        );
    }

    /**
     * @throws Throwable
     */
    public function testExpired(): void
    {
        $currentTime = new DateTimeImmutable();

        $clock = $this->createMock(SystemClock::class);

        $clock->method('getCurrentTime')
            ->willReturn($currentTime);

        $licenseStatus = new LicenseStatus($clock);

        $licenseApi = $this->createMock(LicenseApi::class);

        $licenseApi->expects(self::once())
            ->method('licenseStatus')
            ->willReturn($licenseStatus);

        $expires = new DateTimeImmutable();

        $expires = $expires->setTimestamp(
            strtotime('-1 day')
        );

        $license = new LicenseModel();

        $license->expires = $expires;

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
                                        'title' => 'Foo Title (expired)',
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
            $licenseApi,
        );

        $response = $responder($licenses);

        self::assertSame(
            'twigReturnTest',
            $response->getBody()->__toString()
        );
    }
}
