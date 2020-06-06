<?php

declare(strict_types=1);

namespace Tests\Http\Admin\Software;

use App\Http\Admin\Software\PostAdminSoftwareVersionDeleteAction;
use App\Payload\Payload;
use App\Software\Models\SoftwareModel;
use App\Software\Models\SoftwareVersionModel;
use App\Software\SoftwareApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Flash\Messages as FlashMessages;
use Slim\Psr7\Factory\ResponseFactory;
use Tests\TestConfig;

class PostAdminSoftwareVersionDeleteActionTest extends TestCase
{
    public function testWhenNoSoftwareVersion() : void
    {
        $flashApi = $this->createMock(FlashMessages::class);

        $flashApi->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('PostMessage'),
                [
                    'status' => Payload::STATUS_ERROR,
                    'result' => ['message' => 'Something went wrong trying to delete the software version'],
                ]
            );

        $responseFactory = TestConfig::$di->get(ResponseFactory::class);

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareVersionById')
            ->with(self::equalTo('12'))
            ->willReturn(null);

        $softwareApi->expects(self::never())
            ->method('deleteSoftwareVersion');

        $action = new PostAdminSoftwareVersionDeleteAction(
            $flashApi,
            $responseFactory,
            $softwareApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->willReturn(12);

        $returnResponse = $action($request);

        self::assertSame(303, $returnResponse->getStatusCode());

        $headers = $returnResponse->getHeaders();

        self::assertCount(1, $headers);

        self::assertCount(1, $headers['Location']);

        self::assertSame(
            '/admin/software',
            $headers['Location'][0]
        );
    }

    public function test() : void
    {
        $flashApi = $this->createMock(FlashMessages::class);

        $flashApi->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('PostMessage'),
                [
                    'status' => Payload::STATUS_SUCCESSFUL,
                    'result' => ['message' => 'Version was deleted successfully'],
                ]
            );

        $responseFactory = TestConfig::$di->get(ResponseFactory::class);

        $softwareVersion = new SoftwareVersionModel();

        $software = new SoftwareModel();

        $software->slug = 'foo-software-slug';

        $software->id = 'foo-software-id';

        $software->addVersion($softwareVersion);

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareVersionById')
            ->with(self::equalTo('12'))
            ->willReturn($softwareVersion);

        $softwareApi->expects(self::once())
            ->method('deleteSoftwareVersion')
            ->with(self::equalTo($softwareVersion));

        $action = new PostAdminSoftwareVersionDeleteAction(
            $flashApi,
            $responseFactory,
            $softwareApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->willReturn(12);

        $returnResponse = $action($request);

        self::assertSame(303, $returnResponse->getStatusCode());

        $headers = $returnResponse->getHeaders();

        self::assertCount(1, $headers);

        self::assertCount(1, $headers['Location']);

        self::assertSame(
            '/admin/software/view/foo-software-id',
            $headers['Location'][0]
        );
    }
}
