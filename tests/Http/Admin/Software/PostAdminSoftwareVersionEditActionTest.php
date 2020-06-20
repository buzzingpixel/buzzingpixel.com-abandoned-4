<?php

declare(strict_types=1);

namespace Tests\Http\Admin\Software;

use App\Http\Admin\Software\PostAdminSoftwareVersionEditAction;
use App\Http\Admin\Software\PostAdminSoftwareVersionEditResponder;
use App\Payload\Payload;
use App\Software\Models\SoftwareModel;
use App\Software\Models\SoftwareVersionModel;
use App\Software\SoftwareApi;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Safe\DateTimeImmutable;
use Slim\Exception\HttpNotFoundException;
use Throwable;
use function assert;

class PostAdminSoftwareVersionEditActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testWhenNoSoftware() : void
    {
        $responder = $this->createMock(
            PostAdminSoftwareVersionEditResponder::class
        );

        $responder->expects(self::never())
            ->method(self::anything());

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareVersionById')
            ->with(self::equalTo('foo-id'))
            ->willReturn(null);

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::never())
            ->method(self::anything());

        $action = new PostAdminSoftwareVersionEditAction(
            $responder,
            $softwareApi,
            $userApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $exception = null;

        try {
            $action($request);
        } catch (HttpNotFoundException $e) {
            $exception = $e;
        }

        assert($exception instanceof HttpNotFoundException);

        self::assertSame(
            $request,
            $exception->getRequest(),
        );
    }

    /**
     * @throws Throwable
     */
    public function testPostErrors() : void
    {
        $response = $this->createMock(ResponseInterface::class);

        $responder = $this->createMock(
            PostAdminSoftwareVersionEditResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    Payload $payload,
                    string $versionId,
                    string $softwareId
                ) use (
                    $response
                ) {
                    self::assertSame(
                        Payload::STATUS_NOT_VALID,
                        $payload->getStatus(),
                    );

                    self::assertSame(
                        [
                            'message' => 'There were errors with your submission',
                            'inputMessages' => [
                                'major_version' => 'Major version is required',
                                'version' => 'Version is required',
                                'upgrade_price' => 'Upgrade Price must be specified as integer or float',
                                'released_on' => 'Released On is required',
                            ],
                            'inputValues' => [
                                'major_version' => '',
                                'version' => '',
                                'released_on' => '',
                                'upgrade_price' => '',
                            ],
                        ],
                        $payload->getResult()
                    );

                    self::assertSame(
                        'foo-version-id',
                        $versionId,
                    );

                    self::assertSame(
                        'foo-software-id',
                        $softwareId,
                    );

                    return $response;
                }
            );

        $softwareVersion = new SoftwareVersionModel();

        $softwareVersion->id = 'foo-version-id';

        $software = new SoftwareModel();

        $software->slug = 'foo-software-slug';

        $software->id = 'foo-software-id';

        $software->addVersion($softwareVersion);

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareVersionById')
            ->with(self::equalTo('foo-id'))
            ->willReturn($softwareVersion);

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::never())
            ->method(self::anything());

        $action = new PostAdminSoftwareVersionEditAction(
            $responder,
            $softwareApi,
            $userApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([]);

        $returnResponse = $action($request);

        self::assertSame($response, $returnResponse);
    }

    /**
     * @throws Throwable
     */
    public function testWhenNotUpdated() : void
    {
        $timeZone = new DateTimeZone('US/Eastern');

        $dateTime = DateTimeImmutable::createFromFormat(
            'Y-m-d h:i A',
            '2010-01-02 3:00 PM'
        );

        $dateTime = $dateTime->setTimezone($timeZone);

        $response = $this->createMock(ResponseInterface::class);

        $responder = $this->createMock(
            PostAdminSoftwareVersionEditResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    Payload $payload,
                    string $versionId,
                    string $softwareId
                ) use (
                    $response
                ) {
                    self::assertSame(
                        Payload::STATUS_NOT_UPDATED,
                        $payload->getStatus(),
                    );

                    self::assertSame(
                        ['message' => 'An unknown error occurred'],
                        $payload->getResult()
                    );

                    self::assertSame(
                        'foo-version-id',
                        $versionId,
                    );

                    self::assertSame(
                        'foo-software-id',
                        $softwareId,
                    );

                    return $response;
                }
            );

        $softwareVersion = new SoftwareVersionModel();

        $softwareVersion->id = 'foo-version-id';

        $software = new SoftwareModel();

        $software->slug = 'foo-software-slug';

        $software->id = 'foo-software-id';

        $software->addVersion($softwareVersion);

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareVersionById')
            ->with(self::equalTo('foo-id'))
            ->willReturn($softwareVersion);

        $softwareApi->expects(self::once())
            ->method('saveSoftware')
            ->with(self::equalTo($software))
            ->willReturn(new Payload(Payload::STATUS_ERROR));

        $user = new UserModel();

        $user->timezone = $timeZone;

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        $action = new PostAdminSoftwareVersionEditAction(
            $responder,
            $softwareApi,
            $userApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'major_version' => '6',
                'version' => '6.7.8',
                'released_on' => $dateTime->format('Y-m-d h:i A'),
                'upgrade_price' => '9.34',
            ]);

        $downloadFile = $this->createMock(
            UploadedFileInterface::class
        );

        $request->expects(self::once())
            ->method('getUploadedFiles')
            ->willReturn(
                ['new_download_file' => $downloadFile]
            );

        $returnResponse = $action($request);

        self::assertSame($response, $returnResponse);

        self::assertSame(
            '6',
            $softwareVersion->majorVersion,
        );

        self::assertSame(
            '6.7.8',
            $softwareVersion->version,
        );

        self::assertSame(
            $downloadFile,
            $softwareVersion->newDownloadFile,
        );

        self::assertSame(
            9.34,
            $softwareVersion->upgradePrice,
        );

        $updatedDateTime = $dateTime->setTimezone(
            new DateTimeZone('UTC')
        );

        self::assertSame(
            $updatedDateTime->format('Y-m-d h:i A'),
            $softwareVersion->releasedOn->format('Y-m-d h:i A'),
        );
    }

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $payload = new Payload(Payload::STATUS_UPDATED);

        $timeZone = new DateTimeZone('US/Eastern');

        $dateTime = DateTimeImmutable::createFromFormat(
            'Y-m-d h:i A',
            '2010-01-02 3:00 PM'
        );

        $dateTime = $dateTime->setTimezone($timeZone);

        $response = $this->createMock(ResponseInterface::class);

        $responder = $this->createMock(
            PostAdminSoftwareVersionEditResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    Payload $incomingPayload,
                    string $versionId,
                    string $softwareId
                ) use (
                    $response,
                    $payload
                ) {
                    self::assertSame(
                        $payload,
                        $incomingPayload,
                    );

                    self::assertSame(
                        'foo-version-id',
                        $versionId,
                    );

                    self::assertSame(
                        'foo-software-id',
                        $softwareId,
                    );

                    return $response;
                }
            );

        $softwareVersion = new SoftwareVersionModel();

        $softwareVersion->id = 'foo-version-id';

        $software = new SoftwareModel();

        $software->slug = 'foo-software-slug';

        $software->id = 'foo-software-id';

        $software->addVersion($softwareVersion);

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareVersionById')
            ->with(self::equalTo('foo-id'))
            ->willReturn($softwareVersion);

        $softwareApi->expects(self::once())
            ->method('saveSoftware')
            ->with(self::equalTo($software))
            ->willReturn($payload);

        $user = new UserModel();

        $user->timezone = $timeZone;

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        $action = new PostAdminSoftwareVersionEditAction(
            $responder,
            $softwareApi,
            $userApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'major_version' => '6',
                'version' => '6.7.8',
                'released_on' => $dateTime->format('Y-m-d h:i A'),
                'upgrade_price' => '9.34',
            ]);

        $downloadFile = $this->createMock(
            UploadedFileInterface::class
        );

        $request->expects(self::once())
            ->method('getUploadedFiles')
            ->willReturn(
                ['new_download_file' => $downloadFile]
            );

        $returnResponse = $action($request);

        self::assertSame($response, $returnResponse);

        self::assertSame(
            '6',
            $softwareVersion->majorVersion,
        );

        self::assertSame(
            '6.7.8',
            $softwareVersion->version,
        );

        self::assertSame(
            $downloadFile,
            $softwareVersion->newDownloadFile,
        );

        self::assertSame(
            9.34,
            $softwareVersion->upgradePrice,
        );

        $updatedDateTime = $dateTime->setTimezone(
            new DateTimeZone('UTC')
        );

        self::assertSame(
            $updatedDateTime->format('Y-m-d h:i A'),
            $softwareVersion->releasedOn->format('Y-m-d h:i A'),
        );
    }
}
