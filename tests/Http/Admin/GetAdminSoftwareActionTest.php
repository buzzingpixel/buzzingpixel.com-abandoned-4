<?php

declare(strict_types=1);

namespace Tests\Http\Admin;

use App\Content\Meta\MetaPayload;
use App\Http\Admin\GetAdminResponder;
use App\Http\Admin\GetAdminSoftwareAction;
use App\Software\Models\SoftwareModel;
use App\Software\SoftwareApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use stdClass;
use Throwable;
use function func_get_args;

class GetAdminSoftwareActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $softwareModels = [
            new SoftwareModel(),
            new SoftwareModel(),
        ];

        $response = $this->createMock(
            ResponseInterface::class
        );

        $responderArgHolder       = new stdClass();
        $responderArgHolder->args = [];

        $responder = $this->createMock(GetAdminResponder::class);

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(static function () use (
                $response,
                $responderArgHolder
            ) {
                $responderArgHolder->args = func_get_args();

                return $response;
            });

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchAllSoftware')
            ->willReturn($softwareModels);

        $action = new GetAdminSoftwareAction(
            $responder,
            $softwareApi
        );

        $returnResponse = $action();

        self::assertSame($response, $returnResponse);

        $args = $responderArgHolder->args;

        self::assertCount(2, $args);

        self::assertSame('Admin/Software.twig', $args[0]);

        $context = $args[1];

        self::assertCount(3, $context);

        /** @var MetaPayload $metaPayload */
        $metaPayload = $context['metaPayload'];

        self::assertSame(
            'Software | Admin',
            $metaPayload->getMetaTitle()
        );

        self::assertSame('', $metaPayload->getMetaDescription());

        self::assertSame('software', $context['activeTab']);

        self::assertSame(
            $softwareModels,
            $context['softwareModels']
        );
    }
}
