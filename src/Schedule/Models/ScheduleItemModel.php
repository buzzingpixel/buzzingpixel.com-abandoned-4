<?php

declare(strict_types=1);

namespace App\Schedule\Models;

use App\Payload\Model;
use DateTimeImmutable;

class ScheduleItemModel extends Model
{
    /** @var string */
    private $id = '';

    public function setId(string $id) : void
    {
        $this->id = $id;
    }

    public function getId() : string
    {
        return $this->id;
    }

    /** @var string */
    private $class = '';

    protected function setClass(string $class) : void
    {
        $this->class = $class;
    }

    public function getClass() : string
    {
        return $this->class;
    }

    /** @var string  */
    private $runEvery = '';

    protected function setRunEvery(string $runEvery) : void
    {
        $this->runEvery = $runEvery;
    }

    public function getRunEvery() : string
    {
        return $this->runEvery;
    }

    /** @var bool */
    private $isRunning = false;

    public function setIsRunning(bool $isRunning) : void
    {
        $this->isRunning = $isRunning;
    }

    public function isRunning() : bool
    {
        return $this->isRunning;
    }

    /** @var DateTimeImmutable|null */
    private $lastRunStartAt;

    public function setLastRunStartAt(?DateTimeImmutable $lastRunStartAt) : void
    {
        $this->lastRunStartAt = $lastRunStartAt;
    }

    public function getLastRunStartAt() : ?DateTimeImmutable
    {
        return $this->lastRunStartAt;
    }

    /** @var DateTimeImmutable|null */
    private $lastRunEndAt;

    public function setLastRunEndAt(?DateTimeImmutable $lastRunEndAt) : void
    {
        $this->lastRunEndAt = $lastRunEndAt;
    }

    public function getLastRunEndAt() : ?DateTimeImmutable
    {
        return $this->lastRunEndAt;
    }
}
