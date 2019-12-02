<?php

declare(strict_types=1);

namespace App\HttpRouteMiddleware\RequireAdmin;

use App\Users\UserApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function dd;

class RequireAdminAction
{
    /** @var UserApi */
    private $userApi;

    public function __construct(
        UserApi $userApi
    ) {
        $this->userApi = $userApi;
    }

    public function __invoke(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ) : ResponseInterface {
        $user = $this->userApi->fetchLoggedInUser();

        if (! $user->isAdmin()) {
            // TODO: Implement handling of unauthorized access
            dd('TODO: handle unauthorized');
        }

        return $handler->handle($request);
    }
}
