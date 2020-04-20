<?php

declare(strict_types=1);

namespace App\Email\Services;

use App\Email\Models\EmailModel;
use App\Queue\Models\QueueItemModel;
use App\Queue\Models\QueueModel;
use App\Queue\QueueApi;

class QueueEmail
{
    private QueueApi $queueApi;

    public function __construct(QueueApi $queueApi)
    {
        $this->queueApi = $queueApi;
    }

    public function __invoke(EmailModel $emailModel) : void
    {
        $queueModel              = new QueueModel();
        $queueModel->handle      = 'send-email';
        $queueModel->displayName = 'Send Email';

        $queueItem          = new QueueItemModel();
        $queueItem->class   = SendQueueEmail::class;
        $queueItem->context = ['model' => $emailModel];

        $queueModel->addItem($queueItem);

        $this->queueApi->addToQueue($queueModel);
    }
}
