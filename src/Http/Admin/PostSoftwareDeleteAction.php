<?php

declare(strict_types=1);

namespace App\Http\Admin;

use App\Payload\Payload;
use App\Software\SoftwareApi;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Flash\Messages as FlashMessages;

class PostSoftwareDeleteAction
{
    /** @var FlashMessages */
    private $flashMessages;
    /** @var ResponseFactoryInterface */
    private $responseFactory;
    /** @var SoftwareApi */
    private $softwareApi;

    public function __construct(
        FlashMessages $flashMessages,
        ResponseFactoryInterface $responseFactory,
        SoftwareApi $softwareApi
    ) {
        $this->flashMessages   = $flashMessages;
        $this->responseFactory = $responseFactory;
        $this->softwareApi     = $softwareApi;
    }

    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $postData = $request->getParsedBody();

        $software = $this->softwareApi->fetchSoftwareBySlug(
            $slug = (string) ($postData['slug'] ?? '')
        );

        if ($software === null) {
            $this->flashMessages->addMessage(
                'PostMessage',
                [
                    'status' => Payload::STATUS_ERROR,
                    'result' => ['message' => 'Something went wrong trying to delete the software'],
                ]
            );
        } else {
            $this->flashMessages->addMessage(
                'PostMessage',
                [
                    'status' => Payload::STATUS_SUCCESSFUL,
                    'result' => ['message' => 'Software was deleted successfully'],
                ]
            );
        }

        $this->softwareApi->deleteSoftware($software);

        return $this->responseFactory->createResponse(303)
            ->withHeader('Location', '/admin/software');
    }
}
