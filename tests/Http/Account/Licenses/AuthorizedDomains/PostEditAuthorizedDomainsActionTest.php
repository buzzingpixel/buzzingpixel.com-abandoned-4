<?php

declare(strict_types=1);

namespace Tests\Http\Account\Licenses\AuthorizedDomains;

use App\Http\Account\Licenses\AuthorizedDomains\PostEditAuthorizedDomainsAction;
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

class PostEditAuthorizedDomainsActionTest extends TestCase
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

        $action = new PostEditAuthorizedDomainsAction(
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
            ->willReturn(['authorized_domains' => "test1\ntest2\n\rtest3\rtest4"]);

        $action = new PostEditAuthorizedDomainsAction(
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
            '/account/licenses/authorized-domains/foo-id',
            $locationHeader[0],
        );

        self::assertSame(
            [
                'test1',
                'test2',
                'test3',
                'test4',
            ],
            $license->authorizedDomains
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
                        'result' => ['message' => 'Authorized domains updated successfully'],
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
            ->willReturn(['authorized_domains' => "foo\nbar\n\r"]);

        $action = new PostEditAuthorizedDomainsAction(
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
            [
                'foo',
                'bar',
            ],
            $license->authorizedDomains
        );
    }
}
