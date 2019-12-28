<?php

declare(strict_types=1);

namespace Tests\Http\Admin;

use App\Http\Admin\PostAdminSoftwareCreateAction;
use App\Http\Admin\PostAdminSoftwareCreateResponder;
use App\Payload\Payload;
use App\Software\Models\SoftwareModel;
use App\Software\SoftwareApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use stdClass;

class PostAdminSoftwareCreateActionTest extends TestCase
{
    public function testWithErrors() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $responderArgsHolder          = new stdClass();
        $responderArgsHolder->payload = null;

        $responder = $this->createMock(
            PostAdminSoftwareCreateResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(static function (Payload $payload) use (
                $responderArgsHolder,
                $response
            ) {
                $responderArgsHolder->payload = $payload;

                return $response;
            });

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::never())
            ->method(self::anything());

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([]);

        $request->expects(self::once())
            ->method('getUploadedFiles')
            ->willReturn([]);

        $action = new PostAdminSoftwareCreateAction(
            $responder,
            $softwareApi
        );

        $returnResponse = $action($request);

        self::assertSame($response, $returnResponse);

        self::assertInstanceOf(
            Payload::class,
            $responderArgsHolder->payload
        );

        /** @var Payload $payload */
        $payload = $responderArgsHolder->payload;

        self::assertSame(
            Payload::STATUS_NOT_VALID,
            $payload->getStatus()
        );

        self::assertSame(
            'There were errors with your submission',
            $payload->getResult()['message']
        );

        self::assertSame(
            [
                'name' => 'Name is required',
                'slug' => 'Slug is required',
                'major_version' => 'Major version is required',
                'version' => 'Version is required',
            ],
            $payload->getResult()['inputMessages']
        );

        self::assertSame(
            [
                'name' => '',
                'slug' => '',
                'for_sale' => false,
                'major_version' => '',
                'version' => '',
            ],
            $payload->getResult()['inputValues']
        );
    }

    public function testWithVersionMissing() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $responderArgsHolder          = new stdClass();
        $responderArgsHolder->payload = null;

        $responder = $this->createMock(
            PostAdminSoftwareCreateResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(static function ($payload) use (
                $responderArgsHolder,
                $response
            ) {
                $responderArgsHolder->payload = $payload;

                return $response;
            });

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::never())
            ->method(self::anything());

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'name' => 'FooName',
                'slug' => 'FooSlug',
                'major_version' => 'FooMajorVersion',
            ]);

        $request->expects(self::once())
            ->method('getUploadedFiles')
            ->willReturn([]);

        $action = new PostAdminSoftwareCreateAction(
            $responder,
            $softwareApi
        );

        $returnResponse = $action($request);

        self::assertSame($response, $returnResponse);

        self::assertInstanceOf(
            Payload::class,
            $responderArgsHolder->payload
        );

        /** @var Payload $payload */
        $payload = $responderArgsHolder->payload;

        self::assertSame(
            Payload::STATUS_NOT_VALID,
            $payload->getStatus()
        );

        self::assertSame(
            'There were errors with your submission',
            $payload->getResult()['message']
        );

        self::assertSame(
            ['version' => 'Version is required'],
            $payload->getResult()['inputMessages']
        );

        self::assertSame(
            [
                'name' => 'FooName',
                'slug' => 'FooSlug',
                'for_sale' => false,
                'major_version' => 'FooMajorVersion',
                'version' => '',
            ],
            $payload->getResult()['inputValues']
        );
    }

    public function testWithNameMissing() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $responderArgsHolder          = new stdClass();
        $responderArgsHolder->payload = null;

        $responder = $this->createMock(
            PostAdminSoftwareCreateResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(static function ($payload) use (
                $responderArgsHolder,
                $response
            ) {
                $responderArgsHolder->payload = $payload;

                return $response;
            });

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::never())
            ->method(self::anything());

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'slug' => 'FooSlug',
                'major_version' => 'FooMajorVersion',
                'version' => 'FooVersion',
            ]);

        $request->expects(self::once())
            ->method('getUploadedFiles')
            ->willReturn([]);

        $action = new PostAdminSoftwareCreateAction(
            $responder,
            $softwareApi
        );

        $returnResponse = $action($request);

        self::assertSame($response, $returnResponse);

        self::assertInstanceOf(
            Payload::class,
            $responderArgsHolder->payload
        );

        /** @var Payload $payload */
        $payload = $responderArgsHolder->payload;

        self::assertSame(
            Payload::STATUS_NOT_VALID,
            $payload->getStatus()
        );

        self::assertSame(
            'There were errors with your submission',
            $payload->getResult()['message']
        );

        self::assertSame(
            ['name' => 'Name is required'],
            $payload->getResult()['inputMessages']
        );

        self::assertSame(
            [
                'name' => '',
                'slug' => 'FooSlug',
                'for_sale' => false,
                'major_version' => 'FooMajorVersion',
                'version' => 'FooVersion',
            ],
            $payload->getResult()['inputValues']
        );
    }

    public function testWithNoDownloadFileWhenSaveFails() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $responderArgsHolder          = new stdClass();
        $responderArgsHolder->payload = null;

        $responder = $this->createMock(
            PostAdminSoftwareCreateResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(static function ($payload) use (
                $responderArgsHolder,
                $response
            ) {
                $responderArgsHolder->payload = $payload;

                return $response;
            });

        $softwareApiCallModelHolder        = new stdClass();
        $softwareApiCallModelHolder->model = null;

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('saveSoftware')
            ->willReturnCallback(static function ($softwareModel) use (
                $softwareApiCallModelHolder
            ) {
                $softwareApiCallModelHolder->model = $softwareModel;

                return new Payload(Payload::STATUS_ERROR);
            });

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'name' => 'FooName',
                'slug' => 'FooSlug',
                'for_sale' => 'true',
                'major_version' => 'FooMajorVersion',
                'version' => 'FooVersion',
            ]);

        $request->expects(self::once())
            ->method('getUploadedFiles')
            ->willReturn([]);

        $action = new PostAdminSoftwareCreateAction(
            $responder,
            $softwareApi
        );

        $returnResponse = $action($request);

        self::assertSame($response, $returnResponse);

        self::assertInstanceOf(
            Payload::class,
            $responderArgsHolder->payload
        );

        /** @var Payload $payload */
        $payload = $responderArgsHolder->payload;

        self::assertSame(
            Payload::STATUS_NOT_CREATED,
            $payload->getStatus()
        );

        self::assertSame(
            'An unknown error occurred',
            $payload->getResult()['message']
        );

        self::assertInstanceOf(
            SoftwareModel::class,
            $softwareApiCallModelHolder->model
        );

        /** @var SoftwareModel $model */
        $model = $softwareApiCallModelHolder->model;

        self::assertSame('', $model->getId());

        self::assertSame(
            'FooSlug',
            $model->getSlug()
        );

        self::assertSame(
            'FooName',
            $model->getName()
        );

        $versions = $model->getVersions();

        self::assertCount(1, $versions);

        $version = $versions[0];

        self::assertSame('', $version->getId());

        self::assertSame($model, $version->getSoftware());

        self::assertSame(
            'FooMajorVersion',
            $version->getMajorVersion()
        );

        self::assertSame(
            'FooVersion',
            $version->getVersion()
        );

        self::assertSame(
            '',
            $version->getDownloadFile()
        );

        self::assertNull($version->getNewDownloadFile());

        self::assertNotEmpty($version->getReleasedOn());
    }

    public function testWithDownloadFileWhenSaveSucceeds() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $responderArgsHolder          = new stdClass();
        $responderArgsHolder->payload = null;

        $responder = $this->createMock(
            PostAdminSoftwareCreateResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(static function ($payload) use (
                $responderArgsHolder,
                $response
            ) {
                $responderArgsHolder->payload = $payload;

                return $response;
            });

        $softwareApiCallModelHolder        = new stdClass();
        $softwareApiCallModelHolder->model = null;

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('saveSoftware')
            ->willReturnCallback(static function ($softwareModel) use (
                $softwareApiCallModelHolder
            ) {
                $softwareApiCallModelHolder->model = $softwareModel;

                return new Payload(Payload::STATUS_CREATED);
            });

        $downloadFile = $this->createMock(
            UploadedFileInterface::class
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'name' => 'FooName',
                'slug' => 'FooSlug',
                'for_sale' => 'true',
                'major_version' => 'FooMajorVersion',
                'version' => 'FooVersion',
            ]);

        $request->expects(self::once())
            ->method('getUploadedFiles')
            ->willReturn(['download_file' => $downloadFile]);

        $action = new PostAdminSoftwareCreateAction(
            $responder,
            $softwareApi
        );

        $returnResponse = $action($request);

        self::assertSame($response, $returnResponse);

        self::assertInstanceOf(
            Payload::class,
            $responderArgsHolder->payload
        );

        /** @var Payload $payload */
        $payload = $responderArgsHolder->payload;

        self::assertSame(
            Payload::STATUS_CREATED,
            $payload->getStatus()
        );

        self::assertSame([], $payload->getResult());

        self::assertInstanceOf(
            SoftwareModel::class,
            $softwareApiCallModelHolder->model
        );

        /** @var SoftwareModel $model */
        $model = $softwareApiCallModelHolder->model;

        self::assertSame('', $model->getId());

        self::assertSame(
            'FooSlug',
            $model->getSlug()
        );

        self::assertSame(
            'FooName',
            $model->getName()
        );

        $versions = $model->getVersions();

        self::assertCount(1, $versions);

        $version = $versions[0];

        self::assertSame('', $version->getId());

        self::assertSame($model, $version->getSoftware());

        self::assertSame(
            'FooMajorVersion',
            $version->getMajorVersion()
        );

        self::assertSame(
            'FooVersion',
            $version->getVersion()
        );

        self::assertSame(
            '',
            $version->getDownloadFile()
        );

        self::assertSame(
            $downloadFile,
            $version->getNewDownloadFile()
        );

        self::assertNotEmpty($version->getReleasedOn());
    }
}
