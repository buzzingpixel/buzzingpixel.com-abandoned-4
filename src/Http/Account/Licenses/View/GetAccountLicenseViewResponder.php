<?php

declare(strict_types=1);

namespace App\Http\Account\Licenses\View;

use App\Content\Meta\MetaPayload;
use App\Http\StandardResponderConstructor;
use App\Licenses\Models\LicenseModel;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetAccountLicenseViewResponder extends StandardResponderConstructor
{
    /**
     * @throws Throwable
     */
    public function __invoke(LicenseModel $license) : ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twigEnvironment->render(
            'Http/Account/LicenseView.twig',
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
            ]
        ));

        return $response;
    }
}
