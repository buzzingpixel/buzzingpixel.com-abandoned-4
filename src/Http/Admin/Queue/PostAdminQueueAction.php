<?php

declare(strict_types=1);

namespace App\Http\Admin\Queue;

use App\Payload\Payload;
use App\Queue\QueueApi;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Flash\Messages as FlashMessages;

use function array_keys;
use function assert;
use function count;
use function is_array;

class PostAdminQueueAction
{
    private FlashMessages $flashMessages;
    private ResponseFactoryInterface $responseFactory;
    private QueueApi $queueApi;

    public function __construct(
        FlashMessages $flashMessages,
        ResponseFactoryInterface $responseFactory,
        QueueApi $queueApi
    ) {
        $this->flashMessages   = $flashMessages;
        $this->responseFactory = $responseFactory;
        $this->queueApi        = $queueApi;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        assert(is_array($parsedBody));

        $action = (string) ($parsedBody['action'] ?? '');

        if ($action === '') {
            $this->flashMessages->addMessage(
                'PostMessage',
                [
                    'status' => Payload::STATUS_ERROR,
                    'result' => ['message' => 'An action must be chosen'],
                ]
            );

            return $this->responseFactory->createResponse(303)->withHeader(
                'Location',
                '/admin/queue'
            );
        }

        /** @var array<string, string> $ids */
        $ids = $parsedBody['selected_items'] ?? [];

        if (
            ($action !== 'clear_all' && $action !== 'restart_all') &&
            count($ids) < 1
        ) {
            $this->flashMessages->addMessage(
                'PostMessage',
                [
                    'status' => Payload::STATUS_ERROR,
                    'result' => ['message' => 'Items must be selected'],
                ]
            );

            return $this->responseFactory->createResponse(303)->withHeader(
                'Location',
                '/admin/queue'
            );
        }

        $ids = array_keys($ids);

        switch ($action) {
            case 'restart':
                $this->queueApi->restartQueuesByIds($ids);

                $msg = [
                    'status' => Payload::STATUS_SUCCESSFUL,
                    'result' => ['message' => 'Items restarted'],
                ];

                break;
            case 'restart_all':
                $this->queueApi->restartAllStalledItems();

                $msg = [
                    'status' => Payload::STATUS_SUCCESSFUL,
                    'result' => ['message' => 'All stalled items restarted'],
                ];

                break;
            case 'delete':
                $this->queueApi->deleteQueuesByIds($ids);

                $msg = [
                    'status' => Payload::STATUS_SUCCESSFUL,
                    'result' => ['message' => 'Items deleted'],
                ];

                break;
            case 'clear_all':
                $this->queueApi->clearAllStalledItems();

                $msg = [
                    'status' => Payload::STATUS_SUCCESSFUL,
                    'result' => ['message' => 'All stalled items cleared'],
                ];

                break;
            default:
                $msg = [
                    'status' => Payload::STATUS_ERROR,
                    'result' => ['message' => 'Unrecognized action'],
                ];
        }

        $this->flashMessages->addMessage('PostMessage', $msg);

        return $this->responseFactory->createResponse(303)->withHeader(
            'Location',
            '/admin/queue'
        );
    }
}
