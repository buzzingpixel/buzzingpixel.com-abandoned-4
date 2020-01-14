<?php

declare(strict_types=1);

namespace App\Http\Admin;

use App\Payload\Payload;
use App\Software\Models\SoftwareVersionModel;
use App\Software\SoftwareApi;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Flash\Messages as FlashMessages;
use function array_filter;

class PostDeleteSoftwareVersionAction
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
            (string) ($postData['software_slug'] ?? '')
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

        $id = (string) ($postData['id'] ?? '');

        $versionMatch = array_filter(
            $software->getVersions(),
            static function (SoftwareVersionModel $model) use ($id) : bool {
                return $model->getId() === $id;
            }
        );

        if (! isset($versionMatch[0])) {
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

        $this->softwareApi->deleteSoftwareVersion($versionMatch[0]);

        $this->flashMessages->addMessage(
            'PostMessage',
            [
                'status' => Payload::STATUS_SUCCESSFUL,
                'result' => ['message' => 'Version was deleted successfully'],
            ]
        );

        return $this->responseFactory->createResponse(303)
            ->withHeader(
                'Location',
                '/admin/software/view/' . $software->getSlug()
            );
    }
}
