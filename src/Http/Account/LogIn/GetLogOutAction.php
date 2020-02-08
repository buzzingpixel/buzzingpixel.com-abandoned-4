<?php

declare(strict_types=1);

namespace App\Http\Account\LogIn;

use App\Users\UserApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ResponseFactory;
use function assert;
use function is_array;

class GetLogOutAction
{
    private UserApi $userApi;
    private ResponseFactory $responseFactory;

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

        assert(is_array($postData));

        $redirectTo = (string) ($postData['redirect_to'] ?? '/');

        return $this->responseFactory->createResponse(303)
            ->withHeader('Location', $redirectTo);
    }
}
