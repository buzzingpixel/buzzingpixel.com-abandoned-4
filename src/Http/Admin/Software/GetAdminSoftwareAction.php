<?php

declare(strict_types=1);

namespace App\Http\Admin\Software;

use App\Content\Meta\MetaPayload;
use App\Http\Admin\GetAdminResponder;
use App\Software\Models\SoftwareModel;
use App\Software\SoftwareApi;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use function array_map;

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
        $software = array_map(
            static function (SoftwareModel $software) {
                return [
                    'href' => '/admin/software/view/' . $software->id,
                    'title' => $software->name,
                    'subtitle' => $software->slug,
                ];
            },
            $this->softwareApi->fetchAllSoftware()
        );

        return ($this->responder)(
            'Http/Admin/Software.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Software | Admin']
                ),
                'activeTab' => 'software',
                'heading' => 'Software Admin',
                'groups' => [['items' => $software]],
            ],
        );
    }
}
