<?php

declare(strict_types=1);

namespace App\Http\Admin\Software;

use App\Payload\Payload;
use App\Software\SoftwareApi;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Flash\Messages as FlashMessages;

class PostAdminSoftwareDeleteAction
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
        $software = $this->softwareApi->fetchSoftwareById(
            $slug = (string) $request->getAttribute('id')
        );

        if ($software === null) {
            $this->flashMessages->addMessage(
                'PostMessage',
                [
                    'status' => Payload::STATUS_ERROR,
                    'result' => ['message' => 'Something went wrong trying to delete the software'],
                ]
            );

            return $this->responseFactory->createResponse(303)
                ->withHeader('Location', '/admin/software');
        }

        $this->softwareApi->deleteSoftware($software);

        $this->flashMessages->addMessage(
            'PostMessage',
            [
                'status' => Payload::STATUS_SUCCESSFUL,
                'result' => ['message' => 'Software was deleted successfully'],
            ]
        );

        return $this->responseFactory->createResponse(303)
            ->withHeader('Location', '/admin/software');
    }
}
