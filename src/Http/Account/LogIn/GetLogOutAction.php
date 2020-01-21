<?php

declare(strict_types=1);

namespace App\Http\Account\LogIn;

use App\Users\UserApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ResponseFactory;

class GetLogOutAction
{
    /** @var UserApi */
    private $userApi;
    /** @var ResponseFactory */
    private $responseFactory;

    public function __construct(
        UserApi $userApi,
        ResponseFactory $responseFactory
    ) {
        $this->userApi         = $userApi;
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $this->userApi->logCurrentUserOut();

        $postData = $request->getParsedBody();

        $redirectTo = (string) ($postData['redirect_to'] ?? '/');

        return $this->responseFactory->createResponse(303)
            ->withHeader('Location', $redirectTo);
    }
}