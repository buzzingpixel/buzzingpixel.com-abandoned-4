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

class GetAdminSoftwareAddVersionAction
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
        $software = $this->softwareApi->fetchSoftwareById(
            (string) $request->getAttribute('id')
        );

        if ($software === null) {
            throw new HttpNotFoundException($request);
        }

        return ($this->responder)(
            'Admin/SoftwareAddVersion.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Create new version for ' . $software->name . ' | Admin']
                ),
                'activeTab' => 'software',
                'breadcrumbs' => [
                    [
                        'href' => '/admin/software',
                        'content' => 'Software Admin',
                    ],
                    [
                        'href' => '/admin/software/view/' .
                            $software->slug,
                        'content' => $software->name,
                    ],
                    ['content' => 'Add Version'],
                ],
                'software' => $software,
            ],
        );
    }
}
