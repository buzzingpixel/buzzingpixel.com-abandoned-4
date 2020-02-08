<?php

declare(strict_types=1);

namespace App\Schedule\Models;

use App\Payload\Model;
use DateTimeImmutable;
use InvalidArgumentException;
use function gettype;
use function in_array;

class ScheduleItemModel extends Model
{
    private string $id = '';

    public function setId(string $id) : void
    {
        $this->id = $id;
    }

    public function getId() : string
    {
        return $this->id;
    }

    private string $class = '';

    protected function setClass(string $class) : void
    {
        $this->class = $class;
    }

    public function getClass() : string
    {
        return $this->class;
    }

    /** @var float|int|string  */
    private $runEvery = '';

    /**
     * @param float|int|string $runEvery
     */
    protected function setRunEvery($runEvery) : void
    {
        $type = gettype($runEvery);

        $allowed = ['float', 'integer', 'string'];

        if (! in_array($type, $allowed, true)) {
            throw new InvalidArgumentException(
                'RunEvery must be a float, integer, or string'
            );
        }

        $this->runEvery = $runEvery;
    }

    /**
     * @return float|int|string
     */
    public function getRunEvery()
    {
        return $this->runEvery;
    }

    private bool $isRunning = false;

    public function setIsRunning(bool $isRunning) : void
    {
        $this->isRunning = $isRunning;
    }

    public function isRunning() : bool
    {
        return $this->isRunning;
    }

    private ?DateTimeImmutable $lastRunStartAt = null;

    public function setLastRunStartAt(?DateTimeImmutable $lastRunStartAt) : void
    {
        $this->lastRunStartAt = $lastRunStartAt;
    }

    public function getLastRunStartAt() : ?DateTimeImmutable
    {
        return $this->lastRunStartAt;
    }

    private ?DateTimeImmutable $lastRunEndAt = null;

    public function setLastRunEndAt(?DateTimeImmutable $lastRunEndAt) : void
    {
        $this->lastRunEndAt = $lastRunEndAt;
    }

    public function getLastRunEndAt() : ?DateTimeImmutable
    {
        return $this->lastRunEndAt;
    }
}
