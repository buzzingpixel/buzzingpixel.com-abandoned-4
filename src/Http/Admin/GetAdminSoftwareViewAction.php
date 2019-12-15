<?php

declare(strict_types=1);

namespace App\Http\Admin;

use App\Content\Meta\MetaPayload;
use App\Software\SoftwareApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;

class GetAdminSoftwareViewAction
{
    /** @var GetAdminResponder */
    private $responder;
    /** @var SoftwareApi */
    private $softwareApi;

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
                    ['metaTitle' => $softwareModel->getName() . ' | Admin']
                ),
                'activeTab' => 'software',
                'breadcrumbs' => [
                    [
                        'href' => '/admin/software',
                        'content' => 'Software Admin',
                    ],
                    ['content' => 'Edit Software'],
                ],
                'softwareModel' => $softwareModel,
            ],
        );
    }
}
