<?php

declare(strict_types=1);

namespace App\Http\Admin\Software;

use App\Payload\Payload;
use App\Software\Models\SoftwareVersionModel;
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

class PostAdminSoftwareAddVersionAction
{
    private SoftwareApi $softwareApi;
    private PostAdminSoftwareAddVersionResponder $responder;
    private UserApi $userApi;

    public function __construct(
        SoftwareApi $softwareApi,
        PostAdminSoftwareAddVersionResponder $responder,
        UserApi $userApi
    ) {
        $this->softwareApi = $softwareApi;
        $this->responder   = $responder;
        $this->userApi     = $userApi;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $software = $this->softwareApi->fetchSoftwareById(
            (string) $request->getAttribute('id')
        );

        if ($software === null) {
            throw new HttpNotFoundException($request);
        }

        $user = $this->userApi->fetchLoggedInUser();

        assert($user instanceof UserModel);

        $now = new DateTimeImmutable(
            'now',
            $user->timezone
        );

        $postData = $request->getParsedBody();

        assert(is_array($postData));

        $inputValues = [
            'major_version' => (string) ($postData['major_version'] ?? ''),
            'version' => (string) ($postData['version'] ?? ''),
            'released_on' => (string) ($postData['released_on'] ?? ''),
            'upgrade_price' => (string) ($postData['upgrade_price'] ?? ''),
        ];

        if ($inputValues['released_on'] === '') {
            $inputValues['released_on'] = $now->format('Y-m-d h:i A');
        }

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
                $software->id,
            );
        }

        /** @psalm-suppress MixedAssignment */
        $downloadFile = $request->getUploadedFiles()['download_file'] ?? null;

        assert(
            $downloadFile instanceof UploadedFileInterface ||
            $downloadFile === null
        );

        $releasedOn = DateTimeImmutable::createFromFormat(
            'Y-m-d h:i A',
            $inputValues['released_on'],
            $user->timezone
        );

        assert($releasedOn instanceof DateTimeImmutable);

        $releasedOn = $releasedOn->setTimezone(
            new DateTimeZone('UTC')
        );

        $version                  = new SoftwareVersionModel();
        $version->majorVersion    = $inputValues['major_version'];
        $version->version         = $inputValues['version'];
        $version->newDownloadFile = $downloadFile;
        $version->upgradePrice    = (float) $inputValues['upgrade_price'];
        $version->releasedOn      = $releasedOn;

        $software->addVersion($version);

        $payload = $this->softwareApi->saveSoftware($software);

        if ($payload->getStatus() !== Payload::STATUS_UPDATED) {
            return ($this->responder)(
                new Payload(
                    Payload::STATUS_NOT_UPDATED,
                    ['message' => 'An unknown error occurred'],
                ),
                $software->id,
            );
        }

        return ($this->responder)(
            $payload,
            $software->id
        );
    }
}
