<?php

declare(strict_types=1);

namespace App\Http\Admin\Software;

use App\Payload\Payload;
use App\Software\Models\SoftwareModel;
use App\Software\Models\SoftwareVersionModel;
use App\Software\SoftwareApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use function assert;
use function count;
use function is_array;
use function is_numeric;

class PostAdminSoftwareCreateAction
{
    private PostAdminSoftwareCreateResponder $responder;
    private SoftwareApi $softwareApi;

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

        assert(is_array($postData));

        $inputValues = [
            'name' => (string) ($postData['name'] ?? ''),
            'slug' => (string) ($postData['slug'] ?? ''),
            'for_sale' => ($postData['for_sale'] ?? '') === 'true',
            'price' => $postData['price'] ?? '',
            'renewal_price' => $postData['renewal_price'] ?? '',
            'subscription' => ($postData['subscription'] ?? '') === 'true',
            'major_version' => (string) ($postData['major_version'] ?? ''),
            'version' => (string) ($postData['version'] ?? ''),
        ];

        /** @psalm-suppress MixedAssignment */
        $downloadFile = $request->getUploadedFiles()['download_file'] ?? null;

        assert(
            $downloadFile instanceof UploadedFileInterface ||
            $downloadFile === null
        );

        $inputMessages = [];

        if ($inputValues['name'] === '') {
            $inputMessages['name'] = 'Name is required';
        }

        if ($inputValues['slug'] === '') {
            $inputMessages['slug'] = 'Slug is required';
        }

        if (! is_numeric($inputValues['price'])) {
            $inputMessages['price'] = 'Price must be specified as integer or float';
        }

        if (! is_numeric($inputValues['renewal_price'])) {
            $inputMessages['renewal_price'] = 'Renewal Price must be specified as integer or float';
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

        $version                  = new SoftwareVersionModel();
        $version->majorVersion    = $inputValues['major_version'];
        $version->version         = $inputValues['version'];
        $version->newDownloadFile = $downloadFile;

        $softwareModel                 = new SoftwareModel();
        $softwareModel->name           = $inputValues['name'];
        $softwareModel->slug           = $inputValues['slug'];
        $softwareModel->isForSale      = $inputValues['for_sale'];
        $softwareModel->price          = (float) $inputValues['price'];
        $softwareModel->renewalPrice   = (float) $inputValues['renewal_price'];
        $softwareModel->isSubscription = $inputValues['subscription'];
        $softwareModel->addVersion($version);

        $payload = $this->softwareApi->saveSoftware($softwareModel);

        if ($payload->getStatus() !== Payload::STATUS_CREATED) {
            return ($this->responder)(
                new Payload(
                    Payload::STATUS_NOT_CREATED,
                    ['message' => 'An unknown error occurred'],
                ),
            );
        }

        return ($this->responder)($payload);
    }
}
