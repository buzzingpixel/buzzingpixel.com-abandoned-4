<?php

declare(strict_types=1);

namespace App\HttpRouteMiddleware\RequireAdmin;

use App\Content\Meta\MetaPayload;
use App\Users\UserApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class RequireAdminAction
{
    private RequireAdminResponder $responder;
    private UserApi $userApi;

    public function __construct(
        RequireAdminResponder $responder,
        UserApi $userApi
    ) {
        $this->responder = $responder;
        $this->userApi   = $userApi;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ) : ResponseInterface {
        $user = $this->userApi->fetchLoggedInUser();

        if ($user === null || ! $user->isAdmin()) {
            return ($this->responder)(
                new MetaPayload(
                    ['metaTitle' => 'Unauthorized']
                )
            );
        }

        return $handler->handle($request);
    }
}
