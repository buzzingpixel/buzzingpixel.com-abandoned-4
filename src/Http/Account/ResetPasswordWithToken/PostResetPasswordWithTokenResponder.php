<?php

declare(strict_types=1);

namespace App\Http\Account\ResetPasswordWithToken;

use App\Payload\Payload;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Flash\Messages as FlashMessages;

class PostResetPasswordWithTokenResponder
{
    private FlashMessages $flashMessages;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        FlashMessages $flashMessages,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->flashMessages   = $flashMessages;
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(Payload $payload, string $token = '') : ResponseInterface
    {
        if ($payload->getStatus() !== Payload::STATUS_UPDATED) {
            $this->flashMessages->addMessage(
                'LoginFormMessage',
                [
                    'status' => $payload->getStatus(),
                    'result' => $payload->getResult(),
                ]
            );

            return $this->responseFactory->createResponse(303)
                ->withHeader(
                    'Location',
                    '/account/reset-pw-with-token/' . $token,
                );
        }

        $this->flashMessages->addMessage(
            'LoginFormMessage',
            [
                'status' => Payload::STATUS_SUCCESSFUL,
                'result' => [
                    'message' => 'Your password was updated successfully.' .
                    ' You can now log in with your new password.',
                ],
            ]
        );

        return $this->responseFactory->createResponse(303)
            ->withHeader(
                'Location',
                '/account',
            );
    }
}
