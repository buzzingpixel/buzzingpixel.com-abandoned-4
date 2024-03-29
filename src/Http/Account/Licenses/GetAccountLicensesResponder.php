<?php

declare(strict_types=1);

namespace App\Http\Account\Licenses;

use App\Content\Meta\MetaPayload;
use App\Licenses\LicenseApi;
use App\Licenses\Models\LicenseModel;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Twig\Environment as TwigEnvironment;

use function array_values;
use function count;

class GetAccountLicensesResponder
{
    protected ResponseFactoryInterface $responseFactory;
    protected TwigEnvironment $twigEnvironment;
    private LicenseApi $licenseApi;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        TwigEnvironment $twigEnvironment,
        LicenseApi $licenseApi
    ) {
        $this->responseFactory = $responseFactory;
        $this->twigEnvironment = $twigEnvironment;
        $this->licenseApi      = $licenseApi;
    }

    /**
     * @param array<string, LicenseModel[]> $licenses
     *
     * @throws Throwable
     *
     * @noinspection PhpDocSignatureInspection
     */
    public function __invoke(
        array $licenses
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse();

        $groups = [];

        /** @var LicenseModel[] $group */
        foreach (array_values($licenses) as $key => $group) {
            foreach ($group as $license) {
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

                $status = $this->licenseApi->licenseStatus()->statusString(
                    $license
                );

                $groups[$key]['items'][] = [
                    'href' => '/account/licenses/view/' . $license->id,
                    'title' => $license->itemTitle . ' (' . $status . ')',
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
