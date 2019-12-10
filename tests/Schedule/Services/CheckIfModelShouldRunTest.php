<?php

declare(strict_types=1);

namespace Tests\Schedule\Services;

use App\Schedule\Frequency;
use App\Schedule\Models\ScheduleItemModel;
use App\Schedule\Services\CheckIfModelShouldRun;
use App\Schedule\Services\TranslateRunEvery;
use App\Utilities\SystemClock;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\TestConfig;
use Throwable;
use function Safe\strtotime;

class CheckIfModelShouldRunTest extends TestCase
{
    /** @var CheckIfModelShouldRun */
    private $service;

    /** @var SystemClock&MockObject */
    private $systemClock;

    public function testWhenGetCurrentTimeThrowsException() : void
    {
        $this->systemClock->method('getCurrentTime')
            ->willThrowException(new Exception());

        $model = new ScheduleItemModel();

        self::assertFalse($this->service->check($model));
    }

    /**
     * @throws Throwable
     */
    public function testTaskIsRunning() : void
    {
        $this->systemClock->method('getCurrentTime')
            ->willReturn(new DateTimeImmutable());

        $lastRunStartAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('59 minutes ago')
        );

        $model = new ScheduleItemModel([
            'isRunning' => true,
            'lastRunStartAt' => $lastRunStartAt,
        ]);

        self::assertFalse($this->service->check($model));
    }

    /**
     * @throws Throwable
     */
    public function testRunEveryNumericNotYet() : void
    {
        $this->systemClock->method('getCurrentTime')
            ->willReturn(new DateTimeImmutable());

        $lastRunStartAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('4 minutes ago')
        );

        $model = new ScheduleItemModel([
            'isRunning' => false,
            'lastRunStartAt' => $lastRunStartAt,
            'runEvery' => 5,
        ]);

        self::assertFalse($this->service->check($model));
    }

    /**
     * @throws Throwable
     */
    public function testRunEveryNumericWhenTime() : void
    {
        $this->systemClock->method('getCurrentTime')
            ->willReturn(new DateTimeImmutable());

        $lastRunStartAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('6 minutes ago')
        );

        $model = new ScheduleItemModel([
            'isRunning' => false,
            'lastRunStartAt' => $lastRunStartAt,
            'runEvery' => 5,
        ]);

        self::assertTrue($this->service->check($model));
    }

    /**
     * @throws Throwable
     */
    public function testMidnightStringWhenLastRunIsLessThan20Hours() : void
    {
        $this->systemClock->method('getCurrentTime')
            ->willReturn(new DateTimeImmutable());

        $lastRunStartAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('19 hours ago')
        );

        $model = new ScheduleItemModel([
            'isRunning' => false,
            'lastRunStartAt' => $lastRunStartAt,
            'runEvery' => Frequency::DAY_AT_MIDNIGHT,
        ]);

        self::assertFalse($this->service->check($model));
    }

    /**
     * @throws Throwable
     */
    public function testMidnightStringWhenNotMidnight() : void
    {
        $this->systemClock->method('getCurrentTime')
            ->willReturn((new DateTimeImmutable())->setTimestamp(
                strtotime('Tomorrow 11:59 pm')
            ));

        $lastRunStartAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('24 hours ago')
        );

        $model = new ScheduleItemModel([
            'isRunning' => false,
            'lastRunStartAt' => $lastRunStartAt,
            'runEvery' => Frequency::DAY_AT_MIDNIGHT,
        ]);

        self::assertFalse($this->service->check($model));
    }

    /**
     * @throws Throwable
     */
    public function testEveryDayAtMidnight1() : void
    {
        $this->systemClock->method('getCurrentTime')
            ->willReturn((new DateTimeImmutable())->setTimestamp(
                strtotime('Tomorrow 12:00 am')
            ));

        $lastRunStartAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('24 hours ago')
        );

        $model = new ScheduleItemModel([
            'isRunning' => false,
            'lastRunStartAt' => $lastRunStartAt,
            'runEvery' => Frequency::DAY_AT_MIDNIGHT,
        ]);

        self::assertTrue($this->service->check($model));
    }

    /**
     * @throws Throwable
     */
    public function testEveryDayAtMidnight2() : void
    {
        $this->systemClock->method('getCurrentTime')
            ->willReturn((new DateTimeImmutable())->setTimestamp(
                strtotime('+2 Days 12:05 am')
            ));

        $lastRunStartAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('24 hours ago')
        );

        $model = new ScheduleItemModel([
            'isRunning' => false,
            'lastRunStartAt' => $lastRunStartAt,
            'runEvery' => Frequency::DAY_AT_MIDNIGHT,
        ]);

        self::assertTrue($this->service->check($model));
    }

    /**
     * @throws Throwable
     */
    public function testRunOnSaturdayAtMidnightShouldRun() : void
    {
        $this->systemClock->method('getCurrentTime')
            ->willReturn((new DateTimeImmutable())->setTimestamp(
                strtotime('Next Saturday 12:01 am')
            ));

        $lastRunStartAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('24 hours ago')
        );

        $model = new ScheduleItemModel([
            'isRunning' => false,
            'lastRunStartAt' => $lastRunStartAt,
            'runEvery' => Frequency::SATURDAY_AT_MIDNIGHT,
        ]);

        self::assertTrue($this->service->check($model));
    }

    /**
     * @throws Throwable
     */
    public function testRunOnSaturdayAtMidnightShouldNotRun() : void
    {
        $this->systemClock->method('getCurrentTime')
            ->willReturn((new DateTimeImmutable())->setTimestamp(
                strtotime('Next Friday 12:02 am')
            ));

        $lastRunStartAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('24 hours ago')
        );

        $model = new ScheduleItemModel([
            'isRunning' => false,
            'lastRunStartAt' => $lastRunStartAt,
            'runEvery' => Frequency::SATURDAY_AT_MIDNIGHT,
        ]);

        self::assertFalse($this->service->check($model));
    }

    /**
     * @throws Throwable
     */
    public function testRunOnSundayAtMidnightShouldRun() : void
    {
        $this->systemClock->method('getCurrentTime')
            ->willReturn((new DateTimeImmutable())->setTimestamp(
                strtotime('Next Sunday 12:01 am')
            ));

        $lastRunStartAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('24 hours ago')
        );

        $model = new ScheduleItemModel([
            'isRunning' => false,
            'lastRunStartAt' => $lastRunStartAt,
            'runEvery' => Frequency::SUNDAY_AT_MIDNIGHT,
        ]);

        self::assertTrue($this->service->check($model));
    }

    /**
     * @throws Throwable
     */
    public function testRunOnSundayAtMidnightShouldNotRun() : void
    {
        $this->systemClock->method('getCurrentTime')
            ->willReturn((new DateTimeImmutable())->setTimestamp(
                strtotime('Next Saturday 12:02 am')
            ));

        $lastRunStartAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('24 hours ago')
        );

        $model = new ScheduleItemModel([
            'isRunning' => false,
            'lastRunStartAt' => $lastRunStartAt,
            'runEvery' => Frequency::SUNDAY_AT_MIDNIGHT,
        ]);

        self::assertFalse($this->service->check($model));
    }

    /**
     * @throws Throwable
     */
    public function testRunOnMondayAtMidnightShouldRun() : void
    {
        $this->systemClock->method('getCurrentTime')
            ->willReturn((new DateTimeImmutable())->setTimestamp(
                strtotime('Next Monday 12:01 am')
            ));

        $lastRunStartAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('24 hours ago')
        );

        $model = new ScheduleItemModel([
            'isRunning' => false,
            'lastRunStartAt' => $lastRunStartAt,
            'runEvery' => Frequency::MONDAY_AT_MIDNIGHT,
        ]);

        self::assertTrue($this->service->check($model));
    }

    /**
     * @throws Throwable
     */
    public function testRunOnMondayAtMidnightShouldNotRun() : void
    {
        $this->systemClock->method('getCurrentTime')
            ->willReturn((new DateTimeImmutable())->setTimestamp(
                strtotime('Next Sunday 12:02 am')
            ));

        $lastRunStartAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('24 hours ago')
        );

        $model = new ScheduleItemModel([
            'isRunning' => false,
            'lastRunStartAt' => $lastRunStartAt,
            'runEvery' => Frequency::MONDAY_AT_MIDNIGHT,
        ]);

        self::assertFalse($this->service->check($model));
    }

    /**
     * @throws Throwable
     */
    public function testRunOnTuesdayAtMidnightShouldRun() : void
    {
        $this->systemClock->method('getCurrentTime')
            ->willReturn((new DateTimeImmutable())->setTimestamp(
                strtotime('Next Tuesday 12:01 am')
            ));

        $lastRunStartAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('24 hours ago')
        );

        $model = new ScheduleItemModel([
            'isRunning' => false,
            'lastRunStartAt' => $lastRunStartAt,
            'runEvery' => Frequency::TUESDAY_AT_MIDNIGHT,
        ]);

        self::assertTrue($this->service->check($model));
    }

    /**
     * @throws Throwable
     */
    public function testRunOnTuesdayAtMidnightShouldNotRun() : void
    {
        $this->systemClock->method('getCurrentTime')
            ->willReturn((new DateTimeImmutable())->setTimestamp(
                strtotime('Next Monday 12:02 am')
            ));

        $lastRunStartAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('24 hours ago')
        );

        $model = new ScheduleItemModel([
            'isRunning' => false,
            'lastRunStartAt' => $lastRunStartAt,
            'runEvery' => Frequency::TUESDAY_AT_MIDNIGHT,
        ]);

        self::assertFalse($this->service->check($model));
    }

    /**
     * @throws Throwable
     */
    public function testRunOnWednesdayAtMidnightShouldRun() : void
    {
        $this->systemClock->method('getCurrentTime')
            ->willReturn((new DateTimeImmutable())->setTimestamp(
                strtotime('Next Wednesday 12:01 am')
            ));

        $lastRunStartAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('24 hours ago')
        );

        $model = new ScheduleItemModel([
            'isRunning' => false,
            'lastRunStartAt' => $lastRunStartAt,
            'runEvery' => Frequency::WEDNESDAY_AT_MIDNIGHT,
        ]);

        self::assertTrue($this->service->check($model));
    }

    /**
     * @throws Throwable
     */
    public function testRunOnWednesdayAtMidnightShouldNotRun() : void
    {
        $this->systemClock->method('getCurrentTime')
            ->willReturn((new DateTimeImmutable())->setTimestamp(
                strtotime('Next Tuesday 12:02 am')
            ));

        $lastRunStartAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('24 hours ago')
        );

        $model = new ScheduleItemModel([
            'isRunning' => false,
            'lastRunStartAt' => $lastRunStartAt,
            'runEvery' => Frequency::WEDNESDAY_AT_MIDNIGHT,
        ]);

        self::assertFalse($this->service->check($model));
    }

    /**
     * @throws Throwable
     */
    public function testRunOnThursdayAtMidnightShouldRun() : void
    {
        $this->systemClock->method('getCurrentTime')
            ->willReturn((new DateTimeImmutable())->setTimestamp(
                strtotime('Next Thursday 12:01 am')
            ));

        $lastRunStartAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('24 hours ago')
        );

        $model = new ScheduleItemModel([
            'isRunning' => false,
            'lastRunStartAt' => $lastRunStartAt,
            'runEvery' => Frequency::THURSDAY_AT_MIDNIGHT,
        ]);

        self::assertTrue($this->service->check($model));
    }

    /**
     * @throws Throwable
     */
    public function testRunOnThursdayAtMidnightShouldNotRun() : void
    {
        $this->systemClock->method('getCurrentTime')
            ->willReturn((new DateTimeImmutable())->setTimestamp(
                strtotime('Next Wednesday 12:02 am')
            ));

        $lastRunStartAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('24 hours ago')
        );

        $model = new ScheduleItemModel([
            'isRunning' => false,
            'lastRunStartAt' => $lastRunStartAt,
            'runEvery' => Frequency::THURSDAY_AT_MIDNIGHT,
        ]);

        self::assertFalse($this->service->check($model));
    }

    /**
     * @throws Throwable
     */
    public function testRunOnFridayAtMidnightShouldRun() : void
    {
        $this->systemClock->method('getCurrentTime')
            ->willReturn((new DateTimeImmutable())->setTimestamp(
                strtotime('Next Friday 12:01 am')
            ));

        $lastRunStartAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('24 hours ago')
        );

        $model = new ScheduleItemModel([
            'isRunning' => false,
            'lastRunStartAt' => $lastRunStartAt,
            'runEvery' => Frequency::FRIDAY_AT_MIDNIGHT,
        ]);

        self::assertTrue($this->service->check($model));
    }

    /**
     * @throws Throwable
     */
    public function testRunOnFridayAtMidnightShouldNotRun() : void
    {
        $this->systemClock->method('getCurrentTime')
            ->willReturn((new DateTimeImmutable())->setTimestamp(
                strtotime('Next Thursday 12:02 am')
            ));

        $lastRunStartAt = (new DateTimeImmutable())->setTimestamp(
            strtotime('24 hours ago')
        );

        $model = new ScheduleItemModel([
            'isRunning' => false,
            'lastRunStartAt' => $lastRunStartAt,
            'runEvery' => Frequency::FRIDAY_AT_MIDNIGHT,
        ]);

        self::assertFalse($this->service->check($model));
    }

    protected function setUp() : void
    {
        $this->systemClock = $this->createMock(SystemClock::class);

        $this->service = new CheckIfModelShouldRun(
            TestConfig::$di->get(TranslateRunEvery::class),
            $this->systemClock
        );
    }
}
