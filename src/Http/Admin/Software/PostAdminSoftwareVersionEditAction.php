<?php

declare(strict_types=1);

namespace App\Http\Admin\Software;

use App\Payload\Payload;
use App\Software\Models\SoftwareModel;
use App\Software\SoftwareApi;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use DateTimeImmutable;
use DateTimeZone;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;
use function assert;
use function count;
use function is_array;
use function is_numeric;

class PostAdminSoftwareVersionEditAction
{
    private PostAdminSoftwareVersionEditResponder $responder;
    private SoftwareApi $softwareApi;
    private UserApi $userApi;

    public function __construct(
        PostAdminSoftwareVersionEditResponder $responder,
        SoftwareApi $softwareApi,
        UserApi $userApi
    ) {
        $this->responder   = $responder;
        $this->softwareApi = $softwareApi;
        $this->userApi     = $userApi;
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

        $software = $softwareVersion->getSoftware();

        assert($software instanceof SoftwareModel);

        $postData = $request->getParsedBody();

        assert(is_array($postData));

        $inputValues = [
            'major_version' => (string) ($postData['major_version'] ?? ''),
            'version' => (string) ($postData['version'] ?? ''),
            'released_on' => (string) ($postData['released_on'] ?? ''),
            'upgrade_price' => (string) ($postData['upgrade_price'] ?? ''),
        ];

        /** @psalm-suppress MixedAssignment */
        $downloadFile = $request->getUploadedFiles()['new_download_file'] ?? null;
        assert($downloadFile instanceof UploadedFileInterface || $downloadFile === null);

        $inputMessages = [];

        if ($inputValues['major_version'] === '') {
            $inputMessages['major_version'] = 'Major version is required';
        }

        if ($inputValues['version'] === '') {
            $inputMessages['version'] = 'Version is required';
        }

        if (! is_numeric($inputValues['upgrade_price'])) {
            $inputMessages['upgrade_price'] = 'Upgrade Price must be specified as integer or float';
        }

        if ($inputValues['released_on'] === '') {
            $inputMessages['released_on'] = 'Released On is required';
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

        $user = $this->userApi->fetchLoggedInUser();
        assert($user instanceof UserModel);

        $releasedOn = DateTimeImmutable::createFromFormat(
            'Y-m-d h:i A',
            (string) $inputValues['released_on'],
            $user->getTimezone()
        );
        assert($releasedOn instanceof DateTimeImmutable);

        $releasedOn = $releasedOn->setTimezone(
            new DateTimeZone('UTC')
        );

        $softwareVersion->setMajorVersion(
            (string) $inputValues['major_version']
        );

        $softwareVersion->setVersion((string) $inputValues['version']);

        $softwareVersion->setNewDownloadFile($downloadFile);

        $softwareVersion->setUpgradePrice(
            (float) $inputValues['upgrade_price']
        );

        $softwareVersion->setReleasedOn($releasedOn);

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
