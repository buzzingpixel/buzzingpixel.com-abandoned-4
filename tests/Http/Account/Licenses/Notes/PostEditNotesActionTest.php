<?php

declare(strict_types=1);

namespace Tests\Http\Account\Licenses\Notes;

use App\Http\Account\Licenses\Notes\PostEditNotesAction;
use App\Licenses\LicenseApi;
use App\Licenses\Models\LicenseModel;
use App\Payload\Payload;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Flash\Messages as FlashMessages;
use Tests\TestConfig;
use Throwable;
use function assert;

class PostEditNotesActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testWhenNotFound() : void
    {
        $flashMessages = $this->createMock(
            FlashMessages::class
        );

        $flashMessages->expects(self::never())
            ->method(self::anything());

        $licenseApi = $this->createMock(LicenseApi::class);

        $licenseApi->expects(self::once())
            ->method('fetchCurrentUserLicenseById')
            ->with(self::equalTo('foo-id'))
            ->willReturn(null);

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $action = new PostEditNotesAction(
            $licenseApi,
            $flashMessages,
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            ),
        );

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
    public function testWhenNotUpdated() : void
    {
        $license = new LicenseModel();

        $payload = new Payload(Payload::STATUS_ERROR);

        $flashMessages = $this->createMock(
            FlashMessages::class
        );

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('PostMessage'),
                self::equalTo(
                    [
                        'status' => $payload->getStatus(),
                        'result' => ['message' => 'An unknown error occurred'],
                    ]
                ),
            );

        $licenseApi = $this->createMock(LicenseApi::class);

        $licenseApi->expects(self::once())
            ->method('fetchCurrentUserLicenseById')
            ->with(self::equalTo('foo-id'))
            ->willReturn($license);

        $licenseApi->expects(self::once())
            ->method('saveLicense')
            ->with(self::equalTo($license))
            ->willReturn($payload);

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn(['notes' => 'foo-notes']);

        $action = new PostEditNotesAction(
            $licenseApi,
            $flashMessages,
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            ),
        );

        $response = $action($request);

        $headers = $response->getHeaders();

        self::assertCount(1, $headers);

        $locationHeader = $headers['Location'];

        self::assertCount(1, $locationHeader);

        self::assertSame(
            '/account/licenses/notes/foo-id',
            $locationHeader[0],
        );

        self::assertSame(
            'foo-notes',
            $license->notes
        );
    }

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $license = new LicenseModel();

        $payload = new Payload(Payload::STATUS_UPDATED);

        $flashMessages = $this->createMock(
            FlashMessages::class
        );

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('PostMessage'),
                self::equalTo(
                    [
                        'status' => Payload::STATUS_SUCCESSFUL,
                        'result' => ['message' => 'Notes updated successfully'],
                    ]
                ),
            );

        $licenseApi = $this->createMock(LicenseApi::class);

        $licenseApi->expects(self::once())
            ->method('fetchCurrentUserLicenseById')
            ->with(self::equalTo('foo-id'))
            ->willReturn($license);

        $licenseApi->expects(self::once())
            ->method('saveLicense')
            ->with(self::equalTo($license))
            ->willReturn($payload);

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn(['notes' => 'bar-note']);

        $action = new PostEditNotesAction(
            $licenseApi,
            $flashMessages,
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            ),
        );

        $response = $action($request);

        $headers = $response->getHeaders();

        self::assertCount(1, $headers);

        $locationHeader = $headers['Location'];

        self::assertCount(1, $locationHeader);

        self::assertSame(
            '/account/licenses/view/foo-id',
            $locationHeader[0],
        );

        self::assertSame(
            'bar-note',
            $license->notes,
        );
    }
}
