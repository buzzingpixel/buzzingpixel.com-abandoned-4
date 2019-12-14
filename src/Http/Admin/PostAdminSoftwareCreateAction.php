<?php

declare(strict_types=1);

namespace App\Http\Admin;

use App\Payload\Payload;
use App\Software\Models\SoftwareModel;
use App\Software\Models\SoftwareVersionModel;
use App\Software\SoftwareApi;
use App\Users\UserApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function count;

class PostAdminSoftwareCreateAction
{
    /** @var PostAdminSoftwareCreateResponder */
    private $responder;
    /** @var UserApi */
    private $softwareApi;

    public function __construct(
        PostAdminSoftwareCreateResponder $responder,
        SoftwareApi $softwareApi
    ) {
        $this->responder   = $responder;
        $this->softwareApi = $softwareApi;
    }

    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $postData = $request->getParsedBody();

        $inputValues = [
            'name' => $postData['name'] ?? '',
            'slug' => $postData['slug'] ?? '',
            'for_sale' => ($postData['for_sale'] ?? '') === 'true',
            'major_version' => $postData['major_version'] ?? '',
            'version' => $postData['version'] ?? '',
        ];

        $downloadFile = $request->getUploadedFiles()['download_file'] ?? [];

        $inputMessages = [];

        if ($inputValues['name'] === '') {
            $inputMessages['name'] = 'Name is required';
        }

        if ($inputValues['slug'] === '') {
            $inputMessages['slug'] = 'Slug is required';
        }

        if ($inputValues['major_version'] === '') {
            $inputMessages['major_version'] = 'Major version is required';
        }

        if ($inputValues['version'] === '') {
            $inputMessages['version'] = 'Version is required';
        }

        if (count($inputMessages) > 0) {
            return ($this->responder)(
                new Payload(
                    Payload::STATUS_NOT_VALID,
                    [
                        'message' => 'There were errors with your submission',
                        'inputMessages' => $inputMessages,
                        'inputValues' => $inputValues,
                    ],
                ),
            );
        }

        $softwareModel = new SoftwareModel([
            'name' => $inputValues['name'],
            'slug' => $inputValues['slug'],
            'isForSale' => $inputValues['for_sale'],
            'versions' => [
                new SoftwareVersionModel([
                    'majorVersion' => $inputValues['major_version'],
                    'version' => $inputValues['version'],
                ]),
            ],
        ]);

        $payload = $this->softwareApi->saveSoftware($softwareModel);

        if ($payload->getStatus() !== Payload::STATUS_CREATED) {
            return ($this->responder)(
                new Payload(
                    Payload::STATUS_NOT_VALID,
                    ['message' => 'An unknown error occured'],
                ),
            );
        }

        return ($this->responder)($payload);
    }
}
