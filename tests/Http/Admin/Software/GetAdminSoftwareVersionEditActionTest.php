<?php

declare(strict_types=1);

namespace Tests\Http\Admin\Software;

use App\Content\Meta\MetaPayload;
use App\Http\Admin\GetAdminResponder;
use App\Http\Admin\Software\GetAdminSoftwareVersionEditAction;
use App\Software\Models\SoftwareModel;
use App\Software\Models\SoftwareVersionModel;
use App\Software\SoftwareApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use stdClass;
use Throwable;
use function assert;

class GetAdminSoftwareVersionEditActionTest extends TestCase
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
            ->method('fetchSoftwareVersionById')
            ->with(self::equalTo('foo-slug'))
            ->willReturn(null);

        $service = new GetAdminSoftwareVersionEditAction(
            $responder,
            $softwareApi
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-slug');

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

        $softwareVersion = new SoftwareVersionModel();

        $softwareVersion->version = '1.2.3';

        $software = new SoftwareModel();

        $software->addVersion($softwareVersion);

        $software->name = 'Foo Name';

        $software->slug = 'foo-slug';

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareVersionById')
            ->with(self::equalTo('foo-slug'))
            ->willReturn($softwareVersion);

        $service = new GetAdminSoftwareVersionEditAction(
            $responder,
            $softwareApi
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-slug');

        $returnResponse = $service($request);

        self::assertSame($response, $returnResponse);

        self::assertSame(
            'Admin/SoftwareVersionEdit.twig',
            $holder->template
        );

        self::assertCount(4, $holder->context);

        $metaPayload = $holder->context['metaPayload'];

        assert($metaPayload instanceof MetaPayload);

        self::assertSame(
            'Edit Version ' .
                $softwareVersion->version . ' of ' .
                $software->name . ' | Admin',
            $metaPayload->getMetaTitle()
        );

        self::assertSame(
            'software',
            $holder->context['activeTab']
        );

        $breadCrumbs = $holder->context['breadcrumbs'];

        self::assertCount(3, $breadCrumbs);

        self::assertSame(
            [
                [
                    'href' => '/admin/software',
                    'content' => 'Software Admin',
                ],
                [
                    'href' => '/admin/software/view/' .
                        $software->slug,
                    'content' => $software->name,
                ],
                ['content' => 'Edit Version'],
            ],
            $holder->context['breadcrumbs'],
        );

        self::assertSame(
            $softwareVersion,
            $holder->context['softwareVersion'],
        );
    }
}
