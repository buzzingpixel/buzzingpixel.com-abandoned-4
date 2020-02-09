<?php

declare(strict_types=1);

namespace App\Http\Admin\Software;

use App\Content\Meta\MetaPayload;
use App\Http\Admin\GetAdminResponder;
use App\Software\SoftwareApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;

class GetAdminSoftwareViewAction
{
    private GetAdminResponder $responder;
    private SoftwareApi $softwareApi;

    public function __construct(
        GetAdminResponder $responder,
        SoftwareApi $softwareApi
    ) {
        $this->responder   = $responder;
        $this->softwareApi = $softwareApi;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $softwareModel = $this->softwareApi->fetchSoftwareBySlug(
            (string) $request->getAttribute('slug')
        );

        if ($softwareModel === null) {
            throw new HttpNotFoundException($request);
        }

        return ($this->responder)(
            'Admin/SoftwareView.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => $softwareModel->name . ' | Admin']
                ),
                'activeTab' => 'software',
                'breadcrumbs' => [
                    [
                        'href' => '/admin/software',
                        'content' => 'Software Admin',
                    ],
                    ['content' => 'View Software'],
                ],
                'softwareModel' => $softwareModel,
            ],
        );
    }
}
