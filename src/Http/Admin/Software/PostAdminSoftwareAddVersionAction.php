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
use function count;
use function is_numeric;

class PostAdminSoftwareAddVersionAction
{
    /** @var SoftwareApi */
    private $softwareApi;
    /** @var PostAdminSoftwareAddVersionResponder */
    private $responder;
    /** @var UserApi */
    private $userApi;

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
        $software = $this->softwareApi->fetchSoftwareBySlug(
            (string) $request->getAttribute('slug')
        );

        if ($software === null) {
            throw new HttpNotFoundException($request);
        }

        /** @var UserModel $user */
        $user = $this->userApi->fetchLoggedInUser();

        $now = new DateTimeImmutable(
            'now',
            $user->getTimezone()
        );

        $postData = $request->getParsedBody();

        $inputValues = [
            'major_version' => $postData['major_version'] ?? '',
            'version' => $postData['version'] ?? '',
            'released_on' => $postData['released_on'] ?? '',
            'upgrade_price' => $postData['upgrade_price'] ?? '',
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
                $software->getSlug(),
            );
        }

        /** @var UploadedFileInterface|null $downloadFile */
        $downloadFile = $request->getUploadedFiles()['download_file'] ?? null;

        /** @var DateTimeImmutable $releasedOn */
        $releasedOn = DateTimeImmutable::createFromFormat(
            'Y-m-d h:i A',
            (string) $inputValues['released_on'],
            $user->getTimezone()
        );

        $releasedOn = $releasedOn->setTimezone(
            new DateTimeZone('UTC')
        );

        $software->addVersion(
            new SoftwareVersionModel([
                'majorVersion' => $inputValues['major_version'],
                'version' => $inputValues['version'],
                'newDownloadFile' => $downloadFile,
                'upgradePrice' => (float) $inputValues['upgrade_price'],
                'releasedOn' => $releasedOn,
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
