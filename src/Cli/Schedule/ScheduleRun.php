<?php

declare(strict_types=1);

namespace App\Cli\Schedule;

use App\Schedule\Frequency;
use Config\Schedule;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use function array_walk;
use function constant;
use function count;
use function dd;

class ScheduleRun extends Command
{
    /** @var string */
    protected static $defaultName = 'schedule:run';

    /** @var Schedule */
    private $schedule;
    /** @var ContainerInterface */
    private $di;

    public function __construct(
        Schedule $schedule,
        ContainerInterface $di
    ) {
        $this->schedule = $schedule;
        $this->di       = $di;

        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output) : ?int
    {
        $classes = $this->schedule->getScheduleClasses();

        if (count($classes) < 1) {
            $output->writeln(
                '<fg=yellow>There are no scheduled commands set up</>'
            );

            return 0;
        }

        array_walk($classes, [$this, 'processScheduleItem']);

        return 0;
    }

    protected function processScheduleItem(string $class) : void
    {
        try {
            $frequency = constant($class . '::RUN_EVERY');
        } catch (Throwable $e) {
            $frequency = Frequency::ALWAYS;
        }

        dd($frequency);
    }
}
