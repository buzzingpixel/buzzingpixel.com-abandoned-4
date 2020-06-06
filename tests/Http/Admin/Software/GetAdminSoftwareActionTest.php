<?php

declare(strict_types=1);

namespace Tests\Http\Admin\Software;

use App\Content\Meta\MetaPayload;
use App\Http\Admin\GetAdminResponder;
use App\Http\Admin\Software\GetAdminSoftwareAction;
use App\Software\Models\SoftwareModel;
use App\Software\SoftwareApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use stdClass;
use Throwable;
use function assert;
use function func_get_args;

class GetAdminSoftwareActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $software1       = new SoftwareModel();
        $software1->name = 'Software 1';
        $software1->slug = 'software-1';

        $software2       = new SoftwareModel();
        $software2->name = 'Software 2';
        $software2->slug = 'software-2';

        $softwareModels = [
            $software1,
            $software2,
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

        self::assertSame('Http/Admin/Software.twig', $args[0]);

        $context = $args[1];

        self::assertCount(4, $context);

        $metaPayload = $context['metaPayload'];

        assert($metaPayload instanceof MetaPayload);

        self::assertSame(
            'Software | Admin',
            $metaPayload->getMetaTitle()
        );

        self::assertSame('', $metaPayload->getMetaDescription());

        self::assertSame('software', $context['activeTab']);

        self::assertSame('Software Admin', $context['heading']);

        self::assertSame(
            [
                [
                    'items' => [
                        [
                            'href' => '/admin/software/view/',
                            'title' => 'Software 1',
                            'subtitle' => 'software-1',
                        ],
                        [
                            'href' => '/admin/software/view/',
                            'title' => 'Software 2',
                            'subtitle' => 'software-2',
                        ],
                    ],
                ],
            ],
            $context['groups']
        );
    }
}
