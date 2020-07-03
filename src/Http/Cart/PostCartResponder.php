<?php

declare(strict_types=1);

namespace App\Http\Cart;

use App\Payload\Payload;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Flash\Messages as FlashMessages;

class PostCartResponder
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

    public function __invoke(Payload $payload): ResponseInterface
    {
        if ($payload->getStatus() !== Payload::STATUS_SUCCESSFUL) {
            $this->flashMessages->addMessage(
                'PostMessage',
                ['status' => $payload->getStatus()],
            );

            return $this->responseFactory->createResponse(303)
                ->withHeader(
                    'Location',
                    '/cart'
                );
        }

        $this->flashMessages->addMessage(
            'PostMessage',
            ['status' => Payload::STATUS_SUCCESSFUL],
        );

        return $this->responseFactory->createResponse(303)
            ->withHeader(
                'Location',
                '/account/purchases/view/' .
                    ((string) $payload->getResult()['orderId']),
            );
    }
}
