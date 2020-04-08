<?php

declare(strict_types=1);

namespace App\Http\Admin\Queue;

use App\Content\Meta\MetaPayload;
use App\Http\Admin\GetAdminResponder;
use App\Queue\QueueApi;
use Psr\Http\Message\ResponseInterface;

class GetAdminQueueAction
{
    private GetAdminResponder $responder;
    private QueueApi $queueApi;

    public function __construct(
        GetAdminResponder $responder,
        QueueApi $queueApi
    ) {
        $this->responder = $responder;
        $this->queueApi  = $queueApi;
    }

    public function __invoke() : ResponseInterface
    {
        return ($this->responder)(
            'Admin/Queue.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Queue | Admin']
                ),
                'activeTab' => 'queue',
                'stalledItems' => $this->queueApi->fetchStalledItems(),
                'incompleteItems' => $this->queueApi->fetchIncomplete(),
            ],
        );
    }
}
