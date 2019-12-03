<?php

declare(strict_types=1);

namespace App\HttpRouteMiddleware\RequireLogIn;

use App\Content\Meta\MetaPayload;
use App\Users\UserApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class RequireLogInAction
{
    /** @var UserApi */
    private $userApi;
    /** @var RequireLoginResponder */
    private $responder;

    public function __construct(
        UserApi $userApi,
        RequireLoginResponder $responder
    ) {
        $this->userApi   = $userApi;
        $this->responder = $responder;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ) : ResponseInterface {
        $user = $this->userApi->fetchLoggedInUser();

        if ($user === null) {
            return ($this->responder)(
                new MetaPayload(['metaTitle' => 'Log In']),
                $request->getUri()->getPath()
            );
        }

        return $handler->handle($request);
    }
}
