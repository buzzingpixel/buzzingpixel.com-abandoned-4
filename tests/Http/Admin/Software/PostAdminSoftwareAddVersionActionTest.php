<?php

declare(strict_types=1);

namespace Tests\Http\Admin\Software;

use App\Http\Admin\Software\PostAdminSoftwareAddVersionAction;
use App\Http\Admin\Software\PostAdminSoftwareAddVersionResponder;
use App\Payload\Payload;
use App\Software\Models\SoftwareModel;
use App\Software\SoftwareApi;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Exception\HttpNotFoundException;
use stdClass;
use Throwable;
use function assert;

class PostAdminSoftwareAddVersionActionTest extends TestCase
{
    public function testWhenSoftwareApiReturnsNoVersion() : void
    {
        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareById')
            ->with(self::equalTo('foo-id'))
            ->willReturn(null);

        $responder = $this->createMock(
            PostAdminSoftwareAddVersionResponder::class
        );

        $responder->expects(self::never())
            ->method(self::anything());

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::never())
            ->method(self::anything());

        $service = new PostAdminSoftwareAddVersionAction(
            $softwareApi,
            $responder,
            $userApi
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
            $service($request);
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof HttpNotFoundException);

        self::assertInstanceOf(
            HttpNotFoundException::class,
            $exception
        );

        self::assertSame(
            $request,
            $exception->getRequest()
        );
    }

    /**
     * @throws Throwable
     */
    public function testErrors() : void
    {
        $holder = new stdClass();

        $holder->payload = null;

        $holder->softwareId = null;

        $software = new SoftwareModel();

        $software->slug = 'foo-slug';

        $software->id = 'foo-id';

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareById')
            ->with(self::equalTo('foo-id'))
            ->willReturn($software);

        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostAdminSoftwareAddVersionResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    Payload $payload,
                    string $id
                ) use (
                    $holder,
                    $response
                ) {
                    $holder->payload = $payload;

                    $holder->softwareId = $id;

                    return $response;
                }
            );

        $user = new UserModel();

        $user->timezone = new DateTimeZone('US/Central');

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method(self::equalTo('fetchLoggedInUser'))
            ->willReturn($user);

        $service = new PostAdminSoftwareAddVersionAction(
            $softwareApi,
            $responder,
            $userApi
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

        $returnResponse = $service($request);

        $now = new DateTimeImmutable(
            'now',
            $user->timezone
        );

        self::assertSame(
            $response,
            $returnResponse
        );

        assert($holder->payload instanceof Payload);

        self::assertSame(
            Payload::STATUS_NOT_VALID,
            $holder->payload->getStatus()
        );

        self::assertSame(
            [
                'message' => 'There were errors with your submission',
                'inputMessages' => [
                    'major_version' => 'Major version is required',
                    'version' => 'Version is required',
                    'upgrade_price' => 'Upgrade Price must be specified as integer or float',
                ],
                'inputValues' => [
                    'major_version' => '',
                    'version' => '',
                    'released_on' => $now->format('Y-m-d h:i A'),
                    'upgrade_price' => '',
                ],
            ],
            $holder->payload->getResult()
        );

        self::assertSame(
            $software->id,
            $holder->softwareId
        );
    }

    /**
     * @throws Throwable
     */
    public function testInvalidPayloadResponse() : void
    {
        $holder = new stdClass();

        $holder->payload = null;

        $holder->softwareId = null;

        $software = new SoftwareModel();

        $software->slug = 'foo-slug';

        $software->id = 'foo-id';

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareById')
            ->with(self::equalTo('foo-id'))
            ->willReturn($software);

        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostAdminSoftwareAddVersionResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    Payload $payload,
                    string $id
                ) use (
                    $holder,
                    $response
                ) {
                    $holder->payload = $payload;

                    $holder->softwareId = $id;

                    return $response;
                }
            );

        $user = new UserModel();

        $user->timezone = new DateTimeZone('US/Central');

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method(self::equalTo('fetchLoggedInUser'))
            ->willReturn($user);

        $service = new PostAdminSoftwareAddVersionAction(
            $softwareApi,
            $responder,
            $userApi
        );

        $releasedOn = new DateTimeImmutable(
            '10 year ago',
            $user->timezone
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
                'major_version' => '5',
                'version' => '5.6.7',
                'released_on' => $releasedOn->format('Y-m-d h:i A'),
                'upgrade_price' => '456.78',
            ]);

        $downloadFile = $this->createMock(
            UploadedFileInterface::class
        );

        $request->expects(self::once())
            ->method('getUploadedFiles')
            ->willReturn(['download_file' => $downloadFile]);

        $softwareApi->expects(self::once())
            ->method('saveSoftware')
            ->with(self::equalTo($software))
            ->willReturn(new Payload(Payload::STATUS_NOT_VALID));

        $returnResponse = $service($request);

        self::assertSame(
            $response,
            $returnResponse
        );

        assert($holder->payload instanceof Payload);

        self::assertSame(
            Payload::STATUS_NOT_UPDATED,
            $holder->payload->getStatus()
        );

        self::assertSame(
            ['message' => 'An unknown error occurred'],
            $holder->payload->getResult()
        );

        self::assertSame(
            $software->id,
            $holder->softwareId
        );
    }

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $holder = new stdClass();

        $holder->payload = null;

        $holder->softwareId = null;

        $software = new SoftwareModel();

        $software->slug = 'foo-slug';

        $software->slug = 'foo-id';

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareById')
            ->with(self::equalTo('foo-id'))
            ->willReturn($software);

        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostAdminSoftwareAddVersionResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    Payload $payload,
                    string $id
                ) use (
                    $holder,
                    $response
                ) {
                    $holder->payload = $payload;

                    $holder->softwareId = $id;

                    return $response;
                }
            );

        $user = new UserModel();

        $user->timezone = new DateTimeZone('US/Central');

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method(self::equalTo('fetchLoggedInUser'))
            ->willReturn($user);

        $service = new PostAdminSoftwareAddVersionAction(
            $softwareApi,
            $responder,
            $userApi
        );

        $releasedOn = new DateTimeImmutable(
            '10 year ago',
            $user->timezone
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
                'major_version' => '5',
                'version' => '5.6.7',
                'released_on' => $releasedOn->format('Y-m-d h:i A'),
                'upgrade_price' => '456.78',
            ]);

        $downloadFile = $this->createMock(
            UploadedFileInterface::class
        );

        $request->expects(self::once())
            ->method('getUploadedFiles')
            ->willReturn(['download_file' => $downloadFile]);

        $softwareApi->expects(self::once())
            ->method('saveSoftware')
            ->with(self::equalTo($software))
            ->willReturn(new Payload(Payload::STATUS_UPDATED));

        $returnResponse = $service($request);

        self::assertSame(
            $response,
            $returnResponse
        );

        assert($holder->payload instanceof Payload);

        self::assertSame(
            Payload::STATUS_UPDATED,
            $holder->payload->getStatus()
        );

        self::assertSame(
            $software->id,
            $holder->softwareId
        );
    }
}
