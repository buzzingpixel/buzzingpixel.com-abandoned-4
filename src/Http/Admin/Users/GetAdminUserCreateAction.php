<?php

declare(strict_types=1);

namespace App\Http\Admin\Users;

use App\Content\Meta\MetaPayload;
use App\Http\Admin\GetAdminResponder;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetAdminUserCreateAction
{
    private GetAdminResponder $responder;

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
            'Http/Admin/UserCreate.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Create New User | Admin']
                ),
                'activeTab' => 'users',
                'breadcrumbs' => [
                    [
                        'href' => '/admin/users',
                        'content' => 'Users Admin',
                    ],
                ],
            ],
        );
    }
}
