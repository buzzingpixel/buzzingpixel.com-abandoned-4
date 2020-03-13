<?php

declare(strict_types=1);

namespace App\Http\Account\Licenses\Notes;

use App\Content\Meta\MetaPayload;
use App\Http\StandardResponderConstructor;
use App\Licenses\Models\LicenseModel;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetEditNotesResponder extends StandardResponderConstructor
{
    /**
     * @throws Throwable
     */
    public function __invoke(LicenseModel $license) : ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twigEnvironment->render(
            'Account/LicenseEditNotes.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Edit Notes on License']
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
                    ['content' => 'Edit Notes'],
                ],
                'license' => $license,
            ]
        ));

        return $response;
    }
}
