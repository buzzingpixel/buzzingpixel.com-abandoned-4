<?php

declare(strict_types=1);

namespace App\Http\Account\Licenses;

use App\Content\Meta\MetaPayload;
use App\Http\StandardResponderConstructor;
use App\Licenses\Models\LicenseModel;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetAccountLicensesResponder extends StandardResponderConstructor
{
    /**
     * @param array<string, LicenseModel[]> $licenses
     *
     * @throws Throwable
     *
     * @noinspection PhpDocSignatureInspection
     */
    public function __invoke(
        array $licenses
    ) : ResponseInterface {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twigEnvironment->render(
            'Http/Account/Licenses.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Your Licenses']
                ),
                'activeTab' => 'licenses',
                'licenses' => $licenses,
            ]
        ));

        return $response;
    }
}
