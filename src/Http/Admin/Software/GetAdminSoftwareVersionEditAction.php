<?php

declare(strict_types=1);

namespace App\Http\Admin\Software;

use App\Content\Meta\MetaPayload;
use App\Http\Admin\GetAdminResponder;
use App\Software\Models\SoftwareModel;
use App\Software\SoftwareApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;

class GetAdminSoftwareVersionEditAction
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
        $softwareVersion = $this->softwareApi->fetchSoftwareVersionById(
            (string) $request->getAttribute('id')
        );

        if ($softwareVersion === null) {
            throw new HttpNotFoundException($request);
        }

        /** @var SoftwareModel $software */
        $software = $softwareVersion->getSoftware();

        return ($this->responder)(
            'Admin/SoftwareVersionEdit.twig',
            [
                'metaPayload' => new MetaPayload(
                    [
                        'metaTitle' => 'Edit Version ' .
                        $softwareVersion->getVersion() . ' of ' .
                            $software->getName() . ' | Admin',
                    ]
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
                    ['content' => 'Edit Version'],
                ],
                'softwareVersion' => $softwareVersion,
            ],
        );
    }
}
