<?php

declare(strict_types=1);

namespace Tests\Http\Account\Licenses;

use App\Http\Account\Licenses\GetAccountLicensesAction;
use App\Http\Account\Licenses\GetAccountLicensesResponder;
use App\Licenses\LicenseApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetAccountLicensesActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $response = $this->createMock(ResponseInterface::class);

        $licenseApi = $this->createMock(
            LicenseApi::class
        );

        $licenseApi->expects(self::at(0))
            ->method('fetchCurrentUserLicenses')
            ->willReturn(['fetchCurrentUserLicenses']);

        $licenseApi->expects(self::at(1))
            ->method('organizeLicensesByItemKey')
            ->with(['fetchCurrentUserLicenses'])
            ->willReturn(['organizeLicensesByItemKey']);

        $responder = $this->createMock(
            GetAccountLicensesResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo(
                ['organizeLicensesByItemKey']
            ))
            ->willReturn($response);

        $action = new GetAccountLicensesAction(
            $responder,
            $licenseApi,
        );

        self::assertSame($response, $action());
    }
}
