<?php

declare(strict_types=1);

namespace App\Http\Account\Licenses;

use App\Licenses\LicenseApi;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetAccountLicensesAction
{
    private GetAccountLicensesResponder $responder;
    private LicenseApi $licenseApi;

    public function __construct(
        GetAccountLicensesResponder $responder,
        LicenseApi $licenseApi
    ) {
        $this->responder  = $responder;
        $this->licenseApi = $licenseApi;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(): ResponseInterface
    {
        return ($this->responder)(
            $this->licenseApi->organizeLicensesByItemKey(
                $this->licenseApi->fetchCurrentUserLicenses()
            ),
        );
    }
}
