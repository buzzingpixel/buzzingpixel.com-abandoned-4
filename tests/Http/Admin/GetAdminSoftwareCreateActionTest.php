<?php

declare(strict_types=1);

namespace Tests\Http\Admin;

use App\Content\Meta\MetaPayload;
use App\Http\Admin\GetAdminResponder;
use App\Http\Admin\GetAdminSoftwareCreateAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use stdClass;
use Throwable;
use function func_get_args;

class GetAdminSoftwareCreateActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
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

        $action = new GetAdminSoftwareCreateAction($responder);

        $returnResponse = $action();

        self::assertSame($response, $returnResponse);

        $args = $responderArgHolder->args;

        self::assertCount(2, $args);

        self::assertSame('Admin/SoftwareCreate.twig', $args[0]);

        $context = $args[1];

        self::assertCount(3, $context);

        /** @var MetaPayload $metaPayload */
        $metaPayload = $context['metaPayload'];

        self::assertSame(
            'Create New Software | Admin',
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
                ['content' => 'Create New Software'],
            ],
            $context['breadcrumbs'],
        );
    }
}
