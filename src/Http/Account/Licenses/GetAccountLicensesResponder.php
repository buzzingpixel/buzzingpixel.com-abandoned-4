<?php

declare(strict_types=1);

namespace App\Http\Account\Licenses;

use App\Content\Meta\MetaPayload;
use App\Http\StandardResponderConstructor;
use App\Licenses\Models\LicenseModel;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use function array_values;
use function count;

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

        $groups = [];

        foreach (array_values($licenses) as $key => $group) {
            foreach ($group as $license) {
                /** @var LicenseModel[] $group */
                if (! isset($groups[$key]['title'])) {
                    $groups[$key]['title'] = $license->itemTitle;
                }

                $column2 = "You haven't added any authorized domains";

                if (count($license->authorizedDomains) > 0) {
                    $column2 = [];

                    foreach ($license->authorizedDomains as $domain) {
                        $column2[] = $domain;
                    }
                }

                $groups[$key]['items'][] = [
                    'href' => '/account/licenses/view/' . $license->id,
                    'title' => $license->itemTitle,
                    'subtitle' => $license->id,
                    'column2' => $column2,
                ];
            }
        }

        $response->getBody()->write($this->twigEnvironment->render(
            'Http/Account/Licenses.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Your Licenses']
                ),
                'activeTab' => 'licenses',
                'heading' => 'Licenses',
                'groups' => $groups,
            ]
        ));

        return $response;
    }
}
