<?php

declare(strict_types=1);

namespace App\Http\Account\Licenses\Notes;

use App\Licenses\LicenseApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;

class GetEditNotesAction
{
    private GetEditNotesResponder $responder;
    private LicenseApi $licenseApi;

    public function __construct(
        GetEditNotesResponder $responder,
        LicenseApi $licenseApi
    ) {
        $this->responder  = $responder;
        $this->licenseApi = $licenseApi;
    }

    /**
     * @throws Throwable
     * @throws HttpNotFoundException
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $license = $this->licenseApi->fetchCurrentUserLicenseById(
            (string) $request->getAttribute('id')
        );

        if ($license === null) {
            throw new HttpNotFoundException($request);
        }

        return ($this->responder)($license);
    }
}
