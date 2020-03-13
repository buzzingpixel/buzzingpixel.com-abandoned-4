<?php

declare(strict_types=1);

namespace Tests\Http\Account\Licenses\Notes;

use App\Http\Account\Licenses\Notes\GetEditNotesAction;
use App\Http\Account\Licenses\Notes\GetEditNotesResponder;
use App\Licenses\LicenseApi;
use App\Licenses\Models\LicenseModel;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;
use function assert;

class GetEditNotesActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testWhenNotFound() : void
    {
        $licenseApi = $this->createMock(LicenseApi::class);

        $licenseApi->expects(self::once())
            ->method('fetchCurrentUserLicenseById')
            ->with(self::equalTo('foo-id'))
            ->willReturn(null);

        $responder = $this->createMock(
            GetEditNotesResponder::class
        );

        $responder->expects(self::never())
            ->method(self::anything());

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $action = new GetEditNotesAction(
            $responder,
            $licenseApi,
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
    public function test() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $license = new LicenseModel();

        $licenseApi = $this->createMock(LicenseApi::class);

        $licenseApi->expects(self::once())
            ->method('fetchCurrentUserLicenseById')
            ->with(self::equalTo('bar-id'))
            ->willReturn($license);

        $responder = $this->createMock(
            GetEditNotesResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($license))
            ->willReturn($response);

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('bar-id');

        $action = new GetEditNotesAction(
            $responder,
            $licenseApi,
        );

        self::assertSame($response, $action($request));
    }
}
