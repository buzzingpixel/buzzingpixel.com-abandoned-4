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

class GetAdminSoftwareAddVersion
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
        $software = $this->softwareApi->fetchSoftwareBySlug(
            (string) $request->getAttribute('slug')
        );

        if ($software === null) {
            throw new HttpNotFoundException($request);
        }

        return ($this->responder)(
            'Admin/SoftwareAddVersion.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Create new version for ' . $software->getName() . ' | Admin']
                ),
                'activeTab' => 'software',
                'breadcrumbs' => [
                    [
                        'href' => '/admin/software',
                        'content' => 'Software Admin',
                    ],
                    [
                        'href' => '/admin/software/view/' .
                            $software->getSlug(),
                        'content' => $software->getName(),
                    ],
                    ['content' => 'Add Version'],
                ],
                'software' => $software,
            ],
        );
    }
}
