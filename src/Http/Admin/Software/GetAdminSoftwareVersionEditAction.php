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
use function assert;

class GetAdminSoftwareVersionEditAction
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
        $softwareVersion = $this->softwareApi->fetchSoftwareVersionById(
            (string) $request->getAttribute('id')
        );

        if ($softwareVersion === null) {
            throw new HttpNotFoundException($request);
        }

        $software = $softwareVersion->software;

        assert($software instanceof SoftwareModel);

        return ($this->responder)(
            'Http/Admin/SoftwareVersionEdit.twig',
            [
                'metaPayload' => new MetaPayload(
                    [
                        'metaTitle' => 'Edit Version ' .
                            $softwareVersion->version . ' of ' .
                            $software->name . ' | Admin',
                    ]
                ),
                'activeTab' => 'software',
                'breadcrumbs' => [
                    [
                        'href' => '/admin/software',
                        'content' => 'Software Admin',
                    ],
                    [
                        'href' => '/admin/software/view/' . $software->id,
                        'content' => $software->name,
                    ],
                ],
                'softwareVersion' => $softwareVersion,
            ],
        );
    }
}
