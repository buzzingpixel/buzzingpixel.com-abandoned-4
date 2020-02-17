<?php

declare(strict_types=1);

namespace Tests\Http\Admin\Software;

use App\Http\Admin\Software\PostAdminSoftwareDeleteAction;
use App\Payload\Payload;
use App\Software\Models\SoftwareModel;
use App\Software\SoftwareApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Flash\Messages as FlashMessages;
use Tests\TestConfig;

class PostAdminSoftwareDeleteActionTest extends TestCase
{
    public function testWhenSoftwareApiReturnsNoSoftware() : void
    {
        $flashMessages = $this->createMock(
            FlashMessages::class
        );

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('PostMessage'),
                self::equalTo([
                    'status' => Payload::STATUS_ERROR,
                    'result' => ['message' => 'Something went wrong trying to delete the software'],
                ])
            );

        $responseFactory = TestConfig::$di->get(ResponseFactoryInterface::class);

        $softwareApi = $this->createMock(
            SoftwareApi::class
        );

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareById')
            ->with(self::equalTo('foo-id'))
            ->willReturn(null);

        $action = new PostAdminSoftwareDeleteAction(
            $flashMessages,
            $responseFactory,
            $softwareApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $response = $action($request);

        self::assertSame(
            303,
            $response->getStatusCode(),
        );

        $locationHeader = $response->getHeader('Location');

        self::assertCount(1, $locationHeader);

        self::assertSame(
            '/admin/software',
            $locationHeader[0]
        );
    }

    public function test() : void
    {
        $flashMessages = $this->createMock(
            FlashMessages::class
        );

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('PostMessage'),
                self::equalTo([
                    'status' => Payload::STATUS_SUCCESSFUL,
                    'result' => ['message' => 'Software was deleted successfully'],
                ])
            );

        $responseFactory = TestConfig::$di->get(ResponseFactoryInterface::class);

        $software = new SoftwareModel();

        $softwareApi = $this->createMock(
            SoftwareApi::class
        );

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareById')
            ->with(self::equalTo('foo-id'))
            ->willReturn($software);

        $softwareApi->expects(self::once())
            ->method('deleteSoftware')
            ->with($software);

        $action = new PostAdminSoftwareDeleteAction(
            $flashMessages,
            $responseFactory,
            $softwareApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $response = $action($request);

        self::assertSame(
            303,
            $response->getStatusCode(),
        );

        $locationHeader = $response->getHeader('Location');

        self::assertCount(1, $locationHeader);

        self::assertSame(
            '/admin/software',
            $locationHeader[0]
        );
    }
}
