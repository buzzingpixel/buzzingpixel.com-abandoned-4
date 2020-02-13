<?php

declare(strict_types=1);

namespace Tests\Http\Admin\Software;

use App\Content\Meta\MetaPayload;
use App\Http\Admin\GetAdminResponder;
use App\Http\Admin\Software\GetAdminSoftwareViewAction;
use App\Software\Models\SoftwareModel;
use App\Software\SoftwareApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use stdClass;
use Throwable;
use function func_get_args;

class GetAdminSoftwareViewActionTest extends TestCase
{
    public function testWhenNoSoftwareModel() : void
    {
        $responder = $this->createMock(GetAdminResponder::class);

        $responder->expects(self::never())
            ->method(self::anything());

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareBySlug')
            ->with(self::equalTo('foo-slug'))
            ->willReturn(null);

        $action = new GetAdminSoftwareViewAction(
            $responder,
            $softwareApi
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('slug'))
            ->willReturn('foo-slug');

        /** @var HttpNotFoundException|null $exception */
        $exception = null;

        try {
            $action($request);
        } catch (Throwable $e) {
            $exception = $e;
        }

        self::assertInstanceOf(
            HttpNotFoundException::class,
            $exception
        );

        self::assertSame($request, $exception->getRequest());
    }

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(GetAdminResponder::class);

        $responderArgHolder       = new stdClass();
        $responderArgHolder->args = [];

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(static function () use (
                $response,
                $responderArgHolder
            ) {
                $responderArgHolder->args = func_get_args();

                return $response;
            });

        $softwareModel       = new SoftwareModel();
        $softwareModel->name = 'FooName';

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareBySlug')
            ->with(self::equalTo('bar-slug'))
            ->willReturn($softwareModel);

        $action = new GetAdminSoftwareViewAction(
            $responder,
            $softwareApi
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('slug'))
            ->willReturn('bar-slug');

        $returnResponse = $action($request);

        self::assertSame($response, $returnResponse);

        $args = $responderArgHolder->args;

        self::assertCount(2, $args);

        self::assertSame('Admin/SoftwareView.twig', $args[0]);

        $context = $args[1];

        self::assertCount(4, $context);

        /** @var MetaPayload $metaPayload */
        $metaPayload = $context['metaPayload'];

        self::assertSame(
            'FooName | Admin',
            $metaPayload->getMetaTitle()
        );

        self::assertSame('', $metaPayload->getMetaDescription());

        self::assertSame('software', $context['activeTab']);

        self::assertSame(
            [
                [
                    'href' => '/admin/software',
                    'content' => 'Software Admin',
                ],
                ['content' => 'View Software'],
            ],
            $context['breadcrumbs'],
        );

        self::assertSame(
            $softwareModel,
            $context['softwareModel']
        );
    }
}
