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
use function count;
use function is_numeric;

class PostAdminSoftwareCreateAction
{
    /** @var PostAdminSoftwareCreateResponder */
    private $responder;
    /** @var SoftwareApi */
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
            'price' => $postData['price'] ?? '',
            'renewal_price' => $postData['renewal_price'] ?? '',
            'subscription' => ($postData['subscription'] ?? '') === 'true',
            'major_version' => $postData['major_version'] ?? '',
            'version' => $postData['version'] ?? '',
        ];

        /** @var UploadedFileInterface|null $downloadFile */
        $downloadFile = $request->getUploadedFiles()['download_file'] ?? null;

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

        $softwareModel = new SoftwareModel([
            'name' => $inputValues['name'],
            'slug' => $inputValues['slug'],
            'isForSale' => $inputValues['for_sale'],
            'price' => (float) $inputValues['price'],
            'renewalPrice' => (float) $inputValues['renewal_price'],
            'isSubscription' => $inputValues['subscription'],
            'versions' => [
                new SoftwareVersionModel([
                    'majorVersion' => $inputValues['major_version'],
                    'version' => $inputValues['version'],
                    'newDownloadFile' => $downloadFile,
                ]),
            ],
        ]);

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
