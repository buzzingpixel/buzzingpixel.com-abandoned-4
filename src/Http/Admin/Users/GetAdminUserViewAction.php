<?php

declare(strict_types=1);

namespace App\Http\Admin\Users;

use App\Content\Meta\MetaPayload;
use App\Http\Admin\GetAdminResponder;
use App\Users\UserApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;

class GetAdminUserViewAction
{
    /** @var GetAdminResponder */
    private $responder;
    /** @var UserApi */
    private $userApi;

    public function __construct(
        GetAdminResponder $responder,
        UserApi $userApi
    ) {
        $this->responder = $responder;
        $this->userApi   = $userApi;
    }

    /**
     * @throws HttpNotFoundException
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $user = $this->userApi->fetchUserById(
            (string) $request->getAttribute('id')
        );

        if ($user === null) {
            throw new HttpNotFoundException($request);
        }

        return ($this->responder)(
            'Admin/UserView.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => $user->getEmailAddress() . ' | Admin']
                ),
                'activeTab' => 'users',
                'breadcrumbs' => [
                    [
                        'href' => '/admin/users',
                        'content' => 'Users Admin',
                    ],
                    ['content' => 'View User'],
                ],
                'user' => $user,
            ]
        );
    }
}
