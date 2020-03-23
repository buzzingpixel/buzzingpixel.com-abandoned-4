<?php

declare(strict_types=1);

namespace App\Queue\Models;

use DateTimeImmutable;
use DateTimeZone;

class QueueModel
{
    public function __construct()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->addedAt = new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC')
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->assumeDeadAfter = new DateTimeImmutable(
            '+5 minutes',
            new DateTimeZone('UTC')
        );
    }

    public string $id = '';

    public string $handle = '';

    public string $displayName = '';

    public bool $hasStarted = false;

    public bool $isRunning = false;

    public DateTimeImmutable $assumeDeadAfter;

    public bool $isFinished = false;

    public bool $finishedDueToError = false;

    public string $errorMessage = '';

    public float $percentComplete = 0.0;

    public DateTimeImmutable $addedAt;

    public ?DateTimeImmutable $finishedAt;
}
