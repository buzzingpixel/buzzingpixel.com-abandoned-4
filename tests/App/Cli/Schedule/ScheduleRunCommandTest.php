<?php

declare(strict_types=1);

namespace Tests\App\Cli\Schedule;

use App\Cli\Schedule\ScheduleRunCommand;
use App\Schedule\Models\ScheduleItemModel;
use App\Schedule\Payloads\SchedulesPayload;
use App\Schedule\Services\CheckIfModelShouldRun;
use App\Schedule\Services\FetchSchedules;
use App\Schedule\Services\SaveSchedule;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class ScheduleRunCommandTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testWhenNoSchedule() : void
    {
        $command = new ScheduleRunCommand(
            $this->createMock(ContainerInterface::class),
            $this->mockFetchSchedules(),
            $this->createMock(CheckIfModelShouldRun::class),
            $this->createMock(SaveSchedule::class)
        );

        $output = $this->createMock(OutputInterface::class);

        $output->expects(self::once())
            ->method('writeln')
            ->with(self::equalTo(
                '<fg=yellow>There are no scheduled commands set up</>'
            ));

        $return = $command->execute(
            $this->createMock(InputInterface::class),
            $output
        );

        self::assertSame(0, $return);
    }

    /**
     * @throws Throwable
     */
    public function testWhenSaveScheduleThrowsException() : void
    {
        $shouldRun = $this->createMock(CheckIfModelShouldRun::class);

        $shouldRun->method('check')->willReturn(true);

        $scheduleItemModel = new ScheduleItemModel();

        $schedulesPayload = new SchedulesPayload([
            'schedules' => [$scheduleItemModel],
        ]);

        $saveSchedule = $this->createMock(SaveSchedule::class);

        $saveSchedule->method(self::anything())
            ->willThrowException(new Exception());

        $command = new ScheduleRunCommand(
            $this->createMock(ContainerInterface::class),
            $this->mockFetchSchedules($schedulesPayload),
            $shouldRun,
            $saveSchedule
        );

        $output = $this->createMock(OutputInterface::class);

        $output->expects(self::once())
            ->method('writeln')
            ->with(self::equalTo(
                '<fg=red>An unknown error occurred</>'
            ));

        $return = $command->execute(
            $this->createMock(InputInterface::class),
            $output
        );

        self::assertSame(1, $return);
    }

    /**
     * @return FetchSchedules&MockObject
     *
     * @throws Throwable
     */
    private function mockFetchSchedules(?SchedulesPayload $schedulesPayload = null) : FetchSchedules
    {
        $mock = $this->createMock(FetchSchedules::class);

        if (! $schedulesPayload) {
            $schedulesPayload = new SchedulesPayload();
        }

        $mock->expects(self::once())
            ->method('__invoke')
            ->willReturn($schedulesPayload);

        return $mock;
    }
}
