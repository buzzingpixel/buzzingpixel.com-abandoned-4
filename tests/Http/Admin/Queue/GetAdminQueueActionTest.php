<?php

declare(strict_types=1);

namespace Tests\Http\Admin\Queue;

use App\Content\Meta\MetaPayload;
use App\Http\Admin\GetAdminResponder;
use App\Http\Admin\Queue\GetAdminQueueAction;
use App\Queue\QueueApi;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use function assert;

class GetAdminQueueActionTest extends TestCase
{
    public function test() : void
    {
        $queueApi = $this->createMock(QueueApi::class);

        assert(
            $queueApi instanceof QueueApi,
            $queueApi instanceof MockObject,
        );

        $queueApi->expects(self::once())
            ->method('fetchStalledItems')
            ->willReturn(['stalledItems']);

        $queueApi->expects(self::once())
            ->method('fetchIncomplete')
            ->willReturn(['incompleteItems']);

        $response = $this->createMock(ResponseInterface::class);

        $responder = $this->createMock(GetAdminResponder::class);

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo('Admin/Queue.twig'),
                self::equalTo([
                    'metaPayload' => new MetaPayload(
                        ['metaTitle' => 'Queue | Admin']
                    ),
                    'activeTab' => 'queue',
                    'stalledItems' => ['stalledItems'],
                    'incompleteItems' => ['incompleteItems'],
                ])
            )
            ->willReturn($response);

        assert(
            $responder instanceof GetAdminResponder,
            $responder instanceof MockObject,
        );

        $action = new GetAdminQueueAction(
            $responder,
            $queueApi
        );

        self::assertSame($response, $action());
    }
}
