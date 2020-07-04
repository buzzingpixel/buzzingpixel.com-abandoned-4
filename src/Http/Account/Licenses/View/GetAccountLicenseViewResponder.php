<?php

declare(strict_types=1);

namespace App\Http\Account\Licenses\View;

use App\Content\Meta\MetaPayload;
use App\Licenses\LicenseApi;
use App\Licenses\Models\LicenseModel;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Twig\Environment as TwigEnvironment;

use function ucfirst;

class GetAccountLicenseViewResponder
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
     * @throws Throwable
     */
    public function __invoke(LicenseModel $license): ResponseInterface
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
                'statusString' => ucfirst($this->licenseApi
                    ->licenseStatus()
                    ->statusString($license)),
            ]
        ));

        return $response;
    }
}
