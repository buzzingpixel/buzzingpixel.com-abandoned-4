<?php

declare(strict_types=1);

namespace App\Http\Admin\Software;

use App\Payload\Payload;
use App\Software\Models\SoftwareModel;
use App\Software\SoftwareApi;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Flash\Messages as FlashMessages;
use function assert;

class PostAdminSoftwareVersionDeleteAction
{
    private FlashMessages $flashMessages;
    private ResponseFactoryInterface $responseFactory;
    private SoftwareApi $softwareApi;

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
        $softwareVersion = $this->softwareApi->fetchSoftwareVersionById(
            (string) $request->getAttribute('id')
        );

        if ($softwareVersion === null) {
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

        $this->softwareApi->deleteSoftwareVersion($softwareVersion);

        $this->flashMessages->addMessage(
            'PostMessage',
            [
                'status' => Payload::STATUS_SUCCESSFUL,
                'result' => ['message' => 'Version was deleted successfully'],
            ]
        );

        $software = $softwareVersion->getSoftware();
        assert($software instanceof SoftwareModel);

        return $this->responseFactory->createResponse(303)
            ->withHeader(
                'Location',
                '/admin/software/view/' .
                    $software->slug
            );
    }
}
