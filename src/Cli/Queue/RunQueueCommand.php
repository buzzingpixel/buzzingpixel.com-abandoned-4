<?php

declare(strict_types=1);

namespace App\Cli\Queue;

use App\Queue\QueueApi;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class RunQueueCommand extends Command
{
    private QueueApi $queueApi;
    private LoggerInterface $logger;

    public function __construct(QueueApi $queueApi, LoggerInterface $logger)
    {
        $this->queueApi = $queueApi;
        $this->logger   = $logger;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('queue:run');

        $this->setDescription('Runs the next available item in the queue');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->info('Queue command is running next item in queue');

        $item = $this->queueApi->fetchNextQueueItem();

        if ($item === null) {
            $msg = 'There are no items in the queue';

            $this->logger->info($msg);

            $output->writeln('<fg=green>' . $msg . '</>');

            return 0;
        }

        try {
            $msg = 'Running ' . $item->queue->handle .
                ' (' . $item->queue->id . ') step ' .
                ((string) $item->runOrder) . ' (' .
                $item->id . ')...';

            $this->logger->info($msg);

            $output->writeln('<fg=yellow>' . $msg . '</>');

            $this->queueApi->markAsStarted($item->queue);

            $this->queueApi->runItem($item);

            $this->queueApi->postRun($item);

            $msg2 = 'Finished';

            $this->logger->info($msg2);

            $output->writeln('<fg=green>' . $msg2 . '</>');

            return 0;
        } catch (Throwable $exception) {
            $msg = 'An exception was thrown running ' . $item->queue->handle .
                ' (' . $item->queue->id . ') step ' .
                ((string) $item->runOrder) . ' (' .
                $item->id . ')...';

            $this->logger->error(
                $msg,
                ['exception' => $exception]
            );

            $output->writeln('<fg=red>' . $msg . '</>');

            $this->queueApi->markStoppedDueToError($item->queue, $exception);

            return 1;
        }
    }
}
