<?php

declare(strict_types=1);

namespace Tests\Queue\Services;

use App\Payload\Payload;
use App\Persistence\Queue\QueueRecord;
use App\Persistence\SaveExistingRecord;
use App\Queue\Models\QueueModel;
use App\Queue\Services\MarkItemAsStarted;
use App\Queue\Transformers\TransformQueueModelToRecord;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\TestConfig;
use Throwable;
use function assert;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class MarkItemAsStartedTest extends TestCase
{
    public function testWhenUpdated() : void
    {
        $assumeDeadAfter = new DateTimeImmutable(
            '10 years ago',
            new DateTimeZone('UTC')
        );

        $addedAt = new DateTimeImmutable(
            '15 years ago',
            new DateTimeZone('UTC')
        );

        $finishedAt = new DateTimeImmutable(
            '2 years ago',
            new DateTimeZone('UTC')
        );

        $model                     = new QueueModel();
        $model->id                 = 'modelId';
        $model->handle             = 'modelHandle';
        $model->displayName        = 'modelDisplayName';
        $model->hasStarted         = false;
        $model->isRunning          = false;
        $model->assumeDeadAfter    = $assumeDeadAfter;
        $model->isFinished         = false;
        $model->finishedDueToError = false;
        $model->errorMessage       = 'modelErrorMessage';
        $model->percentComplete    = 3.2;
        $model->addedAt            = $addedAt;
        $model->finishedAt         = $finishedAt;

        $saveExistingRecord = $this->createMock(
            SaveExistingRecord::class
        );

        $saveExistingRecord->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (QueueRecord $record) use (
                    $assumeDeadAfter,
                    $addedAt,
                    $finishedAt
                ) : Payload {
                    self::assertSame(
                        'modelId',
                        $record->id
                    );

                    self::assertSame(
                        'modelHandle',
                        $record->handle
                    );

                    self::assertSame(
                        'modelDisplayName',
                        $record->display_name
                    );

                    self::assertSame(
                        '1',
                        $record->has_started
                    );

                    self::assertSame(
                        '1',
                        $record->is_running
                    );

                    self::assertSame(
                        $assumeDeadAfter->format(
                            DateTimeInterface::ATOM
                        ),
                        $record->assume_dead_after,
                    );

                    self::assertSame(
                        '0',
                        $record->is_finished
                    );

                    self::assertSame(
                        '0',
                        $record->finished_due_to_error
                    );

                    self::assertSame(
                        'modelErrorMessage',
                        $record->error_message
                    );

                    self::assertSame(
                        3.2,
                        $record->percent_complete
                    );

                    self::assertSame(
                        $addedAt->format(
                            DateTimeInterface::ATOM
                        ),
                        $record->added_at,
                    );

                    self::assertSame(
                        $finishedAt->format(
                            DateTimeInterface::ATOM
                        ),
                        $record->finished_at,
                    );

                    return new Payload(Payload::STATUS_UPDATED);
                }
            );

        assert(
            $saveExistingRecord instanceof SaveExistingRecord,
            $saveExistingRecord instanceof MockObject
        );

        $service = new MarkItemAsStarted(
            TestConfig::$di->get(TransformQueueModelToRecord::class),
            $saveExistingRecord
        );

        $service($model);

        self::assertTrue($model->hasStarted);
        self::assertTrue($model->isRunning);
    }

    public function testWhenNotUpdated() : void
    {
        $assumeDeadAfter = new DateTimeImmutable(
            '12 years ago',
            new DateTimeZone('UTC')
        );

        $addedAt = new DateTimeImmutable(
            '7 years ago',
            new DateTimeZone('UTC')
        );

        $finishedAt = new DateTimeImmutable(
            '3 years ago',
            new DateTimeZone('UTC')
        );

        $model                     = new QueueModel();
        $model->id                 = 'modelId';
        $model->handle             = 'modelHandle';
        $model->displayName        = 'modelDisplayName';
        $model->hasStarted         = false;
        $model->isRunning          = false;
        $model->assumeDeadAfter    = $assumeDeadAfter;
        $model->isFinished         = false;
        $model->finishedDueToError = false;
        $model->errorMessage       = 'modelErrorMessage';
        $model->percentComplete    = 3.2;
        $model->addedAt            = $addedAt;
        $model->finishedAt         = $finishedAt;

        $saveExistingRecord = $this->createMock(
            SaveExistingRecord::class
        );

        $saveExistingRecord->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (QueueRecord $record) use (
                    $assumeDeadAfter,
                    $addedAt,
                    $finishedAt
                ) : Payload {
                    self::assertSame(
                        'modelId',
                        $record->id
                    );

                    self::assertSame(
                        'modelHandle',
                        $record->handle
                    );

                    self::assertSame(
                        'modelDisplayName',
                        $record->display_name
                    );

                    self::assertSame(
                        '1',
                        $record->has_started
                    );

                    self::assertSame(
                        '1',
                        $record->is_running
                    );

                    self::assertSame(
                        $assumeDeadAfter->format(
                            DateTimeInterface::ATOM
                        ),
                        $record->assume_dead_after,
                    );

                    self::assertSame(
                        '0',
                        $record->is_finished
                    );

                    self::assertSame(
                        '0',
                        $record->finished_due_to_error
                    );

                    self::assertSame(
                        'modelErrorMessage',
                        $record->error_message
                    );

                    self::assertSame(
                        3.2,
                        $record->percent_complete
                    );

                    self::assertSame(
                        $addedAt->format(
                            DateTimeInterface::ATOM
                        ),
                        $record->added_at,
                    );

                    self::assertSame(
                        $finishedAt->format(
                            DateTimeInterface::ATOM
                        ),
                        $record->finished_at,
                    );

                    return new Payload(Payload::STATUS_ERROR);
                }
            );

        assert(
            $saveExistingRecord instanceof SaveExistingRecord,
            $saveExistingRecord instanceof MockObject
        );

        $service = new MarkItemAsStarted(
            TestConfig::$di->get(TransformQueueModelToRecord::class),
            $saveExistingRecord
        );

        $exception = null;

        try {
            $service($model);
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof Exception);

        self::assertSame(
            'An unknown error occured',
            $exception->getMessage(),
        );

        self::assertTrue($model->hasStarted);
        self::assertTrue($model->isRunning);
    }
}
