<?php

declare(strict_types=1);

namespace App\Http\Admin\Software;

use App\Payload\Payload;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Flash\Messages as FlashMessages;

class PostAdminSoftwareAddVersionResponder
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

    public function __invoke(
        Payload $payload,
        string $softwareSlug
    ) : ResponseInterface {
        if ($payload->getStatus() !== Payload::STATUS_UPDATED) {
            $this->flashMessages->addMessage(
                'PostMessage',
                [
                    'status' => $payload->getStatus(),
                    'result' => $payload->getResult(),
                ]
            );

            return $this->responseFactory->createResponse(303)
                ->withHeader(
                    'Location',
                    '/admin/software/' . $softwareSlug . '/add-version'
                );
        } else {
            $this->flashMessages->addMessage(
                'PostMessage',
                [
                    'status' => Payload::STATUS_SUCCESSFUL,
                    'result' => ['message' => 'Successfully edited software'],
                ]
            );
        }

        return $this->responseFactory->createResponse(303)
            ->withHeader(
                'Location',
                '/admin/software/view/' . $softwareSlug
            );
    }
}
