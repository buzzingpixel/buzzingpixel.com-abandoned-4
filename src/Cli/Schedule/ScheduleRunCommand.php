<?php

declare(strict_types=1);

namespace App\Cli\Schedule;

use App\Schedule\Models\ScheduleItemModel;
use App\Schedule\Services\CheckIfModelShouldRun;
use App\Schedule\Services\FetchSchedules;
use App\Schedule\Services\SaveSchedule;
use DateTimeImmutable;
use DateTimeZone;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use function array_walk;
use function count;

class ScheduleRunCommand extends Command
{
    // phpcs:disable
    protected static $defaultName = 'schedule:run';
    // phpcs:enable

    private ContainerInterface $di;
    private FetchSchedules $fetchSchedules;
    private CheckIfModelShouldRun $checkIfModelShouldRun;
    private SaveSchedule $saveSchedule;

    public function __construct(
        ContainerInterface $di,
        FetchSchedules $fetchSchedules,
        CheckIfModelShouldRun $checkIfModelShouldRun,
        SaveSchedule $saveSchedule
    ) {
        $this->di                    = $di;
        $this->fetchSchedules        = $fetchSchedules;
        $this->checkIfModelShouldRun = $checkIfModelShouldRun;
        $this->saveSchedule          = $saveSchedule;

        parent::__construct();
    }

    /** @psalm-suppress PropertyNotSetInConstructor */
    private OutputInterface $output;

    public function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->output = $output;

        $schedules = ($this->fetchSchedules)();

        if (count($schedules->getSchedules()) < 1) {
            $output->writeln(
                '<fg=yellow>There are no scheduled commands set up</>'
            );

            return 0;
        }

        $scheduleItems = $schedules->getSchedules();

        try {
            array_walk(
                $scheduleItems,
                [$this, 'processScheduleItem']
            );
        } catch (Throwable $e) {
            $output->writeln(
                '<fg=red>An unknown error occurred</>'
            );

            return 1;
        }

        return 0;
    }

    /**
     * @throws Throwable
     */
    protected function processScheduleItem(ScheduleItemModel $model) : void
    {
        try {
            $this->processScheduleItemInner($model);
        } catch (Throwable $e) {
            $model->setIsRunning(false);
            ($this->saveSchedule)($model);

            $this->output->writeln(
                '<fg=red>There was a problem running a scheduled command</>'
            );

            $this->output->writeln(
                '<fg=red>' . $model->getClass() . '</>'
            );
            $this->output->writeln(
                '<fg=red>Message: ' . $e->getMessage() . '</>'
            );
        }
    }

    /**
     * @throws Throwable
     */
    private function processScheduleItemInner(ScheduleItemModel $model) : void
    {
        $shouldRun = $this->checkIfModelShouldRun->check($model);

        if ($model->isRunning() && $shouldRun) {
            $this->output->writeln(
                '<fg=yellow>' . $model->getClass() . ' is currently running</>'
            );

            return;
        }

        if (! $shouldRun) {
            $this->output->writeln(
                '<fg=green>' . $model->getClass() .
                ' does not need to run at this time</>'
            );

            return;
        }

        $dateTime = new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC')
        );

        $model->setIsRunning(true);
        $model->setLastRunStartAt($dateTime);
        ($this->saveSchedule)($model);

        /** @psalm-suppress MixedAssignment */
        $class = $this->di->get($model->getClass());

        /** @psalm-suppress MixedFunctionCall */
        $class();

        $dateTime = new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC')
        );

        $model->setIsRunning(false);
        $model->setLastRunEndAt($dateTime);
        ($this->saveSchedule)($model);

        $this->output->writeln(
            '<fg=green>' . $model->getClass() . ' ran successfully</>'
        );
    }
}
