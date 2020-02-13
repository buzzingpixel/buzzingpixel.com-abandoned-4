<?php

declare(strict_types=1);

namespace Tests\Http\Admin\Software;

use App\Http\Admin\Software\PostAdminSoftwareVersionDeleteAction;
use App\Payload\Payload;
use App\Software\SoftwareApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Flash\Messages as FlashMessages;

class PostAdminDeleteSoftwareVersionActionTest extends TestCase
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

        $response = $this->createMock(
            ResponseInterface::class
        );

        $response->expects(self::once())
            ->method('withHeader')
            ->with(
                self::equalTo('Location'),
                self::equalTo('/admin/software'),
            )
            ->willReturn($response);

        $responseFactory = $this->createMock(
            ResponseFactoryInterface::class
        );

        $responseFactory->expects(self::once())
            ->method('createResponse')
            ->with(self::equalTo(303))
            ->willReturn($response);

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareVersionById')
            ->with(self::equalTo('12'))
            ->willReturn(null);

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

        self::assertSame($returnResponse, $response);
    }
}
