<?php

declare(strict_types=1);

namespace App\Http\Admin\Software;

use App\Payload\Payload;
use App\Software\Models\SoftwareVersionModel;
use App\Software\SoftwareApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;
use function count;

class PostAdminSoftwareAddVersionAction
{
    /** @var SoftwareApi */
    private $softwareApi;
    /** @var PostAdminSoftwareAddVersionResponder */
    private $responder;

    public function __construct(
        SoftwareApi $softwareApi,
        PostAdminSoftwareAddVersionResponder $responder
    ) {
        $this->softwareApi = $softwareApi;
        $this->responder   = $responder;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $software = $this->softwareApi->fetchSoftwareBySlug(
            (string) $request->getAttribute('slug')
        );

        if ($software === null) {
            throw new HttpNotFoundException($request);
        }

        $postData = $request->getParsedBody();

        $inputValues = [
            'major_version' => $postData['major_version'] ?? '',
            'version' => $postData['version'] ?? '',
        ];

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
                $software->getSlug(),
            );
        }

        /** @var UploadedFileInterface|null $downloadFile */
        $downloadFile = $request->getUploadedFiles()['download_file'] ?? null;

        $software->addVersion(
            new SoftwareVersionModel([
                'majorVersion' => $inputValues['major_version'],
                'version' => $inputValues['version'],
                'newDownloadFile' => $downloadFile,
            ]),
        );

        $payload = $this->softwareApi->saveSoftware($software);

        if ($payload->getStatus() !== Payload::STATUS_UPDATED) {
            return ($this->responder)(
                new Payload(
                    Payload::STATUS_NOT_UPDATED,
                    ['message' => 'An unknown error occurred'],
                ),
                $software->getSlug(),
            );
        }

        return ($this->responder)(
            $payload,
            $software->getSlug()
        );
    }
}
