<?php

declare(strict_types=1);

namespace App\Http\Admin\Software;

use App\Payload\Payload;
use App\Software\Models\SoftwareModel;
use App\Software\SoftwareApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;
use function count;

class PostAdminSoftwareVersionEditAction
{
    /** @var PostAdminSoftwareVersionEditResponder */
    private $responder;
    /** @var SoftwareApi */
    private $softwareApi;

    public function __construct(
        PostAdminSoftwareVersionEditResponder $responder,
        SoftwareApi $softwareApi
    ) {
        $this->responder   = $responder;
        $this->softwareApi = $softwareApi;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $softwareVersion = $this->softwareApi->fetchSoftwareVersionById(
            (string) $request->getAttribute('id')
        );

        if ($softwareVersion === null) {
            throw new HttpNotFoundException($request);
        }

        /** @var SoftwareModel $software */
        $software = $softwareVersion->getSoftware();

        $postData = $request->getParsedBody();

        $inputValues = [
            'major_version' => $postData['major_version'] ?? '',
            'version' => $postData['version'] ?? '',
        ];

        /** @var UploadedFileInterface|null $downloadFile */
        $downloadFile = $request->getUploadedFiles()['new_download_file'] ?? null;

        $inputMessages = [];

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
                $softwareVersion->getId(),
                $software->getSlug(),
            );
        }

        $softwareVersion->setMajorVersion(
            (string) $inputValues['major_version']
        );
        $softwareVersion->setVersion((string) $inputValues['version']);
        $softwareVersion->setNewDownloadFile($downloadFile);

        $payload = $this->softwareApi->saveSoftware($software);

        if ($payload->getStatus() !== Payload::STATUS_UPDATED) {
            return ($this->responder)(
                new Payload(
                    Payload::STATUS_NOT_UPDATED,
                    ['message' => 'An unknown error occurred'],
                ),
                $softwareVersion->getId(),
                $software->getSlug(),
            );
        }

        return ($this->responder)(
            $payload,
            $softwareVersion->getId(),
            $software->getSlug(),
        );
    }
}
