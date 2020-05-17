<?php

declare(strict_types=1);

namespace Tests\Http\Admin\Software;

use App\Content\Meta\MetaPayload;
use App\Http\Admin\GetAdminResponder;
use App\Http\Admin\Software\GetAdminSoftwareEditAction;
use App\Software\Models\SoftwareModel;
use App\Software\SoftwareApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use stdClass;
use Throwable;
use function assert;

class GetAdminSoftwareEditActionTest extends TestCase
{
    public function testWhenSoftwareApiReturnsNoVersion() : void
    {
        $responder = $this->createMock(
            GetAdminResponder::class
        );

        $responder->expects(self::never())
            ->method(self::anything());

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareById')
            ->with(self::equalTo('foo-id'))
            ->willReturn(null);

        $service = new GetAdminSoftwareEditAction(
            $responder,
            $softwareApi
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
    public function test() : void
    {
        $holder = new stdClass();

        $holder->template = '';

        $holder->context = [];

        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            GetAdminResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    string $template,
                    array $context
                ) use (
                    $holder,
                    $response
                ) : ResponseInterface {
                    $holder->template = $template;

                    $holder->context = $context;

                    return $response;
                }
            );

        $software = new SoftwareModel();

        $software->name = 'Foo Name';

        $software->slug = 'foo-id';

        $software->id = 'bar-id';

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareById')
            ->with(self::equalTo('foo-id'))
            ->willReturn($software);

        $service = new GetAdminSoftwareEditAction(
            $responder,
            $softwareApi
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $returnResponse = $service($request);

        self::assertSame($response, $returnResponse);

        self::assertSame(
            'Http/Admin/SoftwareEdit.twig',
            $holder->template
        );

        self::assertCount(4, $holder->context);

        $metaPayload = $holder->context['metaPayload'];

        assert($metaPayload instanceof MetaPayload);

        self::assertSame(
            'Edit ' . $software->name . ' | Admin',
            $metaPayload->getMetaTitle()
        );

        self::assertSame(
            'software',
            $holder->context['activeTab']
        );

        $breadCrumbs = $holder->context['breadcrumbs'];

        self::assertCount(2, $breadCrumbs);

        self::assertSame(
            [
                [
                    'href' => '/admin/software',
                    'content' => 'Software Admin',
                ],
                [
                    'href' => '/admin/software/view/bar-id',
                    'content' => 'Foo Name',
                ],
            ],
            $holder->context['breadcrumbs'],
        );

        self::assertSame(
            $software,
            $holder->context['software'],
        );
    }
}
