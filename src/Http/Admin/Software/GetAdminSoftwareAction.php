<?php

declare(strict_types=1);

namespace App\Http\Admin\Software;

use App\Content\Meta\MetaPayload;
use App\Http\Admin\GetAdminResponder;
use App\Software\SoftwareApi;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetAdminSoftwareAction
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
    public function __invoke() : ResponseInterface
    {
        return ($this->responder)(
            'Http/Admin/Software.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Software | Admin']
                ),
                'activeTab' => 'software',
                'softwareModels' => $this->softwareApi->fetchAllSoftware(),
            ],
        );
    }
}
