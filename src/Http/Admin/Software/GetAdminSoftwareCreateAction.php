<?php

declare(strict_types=1);

namespace App\Http\Admin\Software;

use App\Content\Meta\MetaPayload;
use App\Http\Admin\GetAdminResponder;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetAdminSoftwareCreateAction
{
    /** @var GetAdminResponder */
    private $responder;

    public function __construct(GetAdminResponder $responder)
    {
        $this->responder = $responder;
    }

    /**
     * @throws Throwable
     */
    public function __invoke() : ResponseInterface
    {
        return ($this->responder)(
            'Admin/SoftwareCreate.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Create New Software | Admin']
                ),
                'activeTab' => 'software',
                'breadcrumbs' => [
                    [
                        'href' => '/admin/software',
                        'content' => 'Software Admin',
                    ],
                    ['content' => 'Create New Software'],
                ],
            ],
        );
    }
}
