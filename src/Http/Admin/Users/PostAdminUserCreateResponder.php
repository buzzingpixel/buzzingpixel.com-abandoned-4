<?php

declare(strict_types=1);

namespace App\Http\Admin\Users;

use App\Payload\Payload;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Flash\Messages as FlashMessages;

class PostAdminUserCreateResponder
{
    /** @var FlashMessages */
    private $flashMessages;
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    public function __construct(
        FlashMessages $flashMessages,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->flashMessages   = $flashMessages;
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(Payload $payload) : ResponseInterface
    {
        if ($payload->getStatus() !== Payload::STATUS_CREATED) {
            $this->flashMessages->addMessage(
                'PostMessage',
                [
                    'status' => $payload->getStatus(),
                    'result' => $payload->getResult(),
                ]
            );

            return $this->responseFactory->createResponse(303)
                ->withHeader('Location', '/admin/users/create');
        } else {
            $this->flashMessages->addMessage(
                'PostMessage',
                [
                    'status' => Payload::STATUS_SUCCESSFUL,
                    'result' => ['message' => 'Successfully created new user'],
                ]
            );
        }

        return $this->responseFactory->createResponse(303)
            ->withHeader('Location', '/admin/users');
    }
}
