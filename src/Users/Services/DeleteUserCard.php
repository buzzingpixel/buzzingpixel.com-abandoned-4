<?php

declare(strict_types=1);

namespace App\Users\Services;

use App\Payload\Payload;
use App\Persistence\UserCards\UserCardRecord;
use App\Users\Events\DeleteUserCardAfterDelete;
use App\Users\Events\DeleteUserCardBeforeDelete;
use App\Users\Models\UserCardModel;
use Exception;
use PDO;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;

class DeleteUserCard
{
    private EventDispatcherInterface $eventDispatcher;
    private PDO $pdo;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        PDO $pdo
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->pdo             = $pdo;
    }

    public function __invoke(UserCardModel $userCardModel): Payload
    {
        try {
            $this->innerRun($userCardModel);

            return new Payload(Payload::STATUS_DELETED);
        } catch (Throwable $e) {
            $this->pdo->rollBack();

            return new Payload(Payload::STATUS_ERROR);
        }
    }

    /**
     * @throws Throwable
     */
    private function innerRun(UserCardModel $userCardModel): void
    {
        $this->pdo->beginTransaction();

        $beforeEvent = new DeleteUserCardBeforeDelete(
            $userCardModel
        );

        $this->eventDispatcher->dispatch($beforeEvent);

        if (! $beforeEvent->isValid) {
            throw new Exception();
        }

        $statement = $this->pdo->prepare(
            'DELETE FROM ' .
            UserCardRecord::tableName() .
            ' WHERE id=:id'
        );

        if (
            ! $statement->execute(
                [':id' => $userCardModel->id]
            )
        ) {
            throw new Exception();
        }

        $afterEvent = new DeleteUserCardAfterDelete(
            $userCardModel
        );

        $this->eventDispatcher->dispatch($afterEvent);

        if (! $afterEvent->isValid) {
            throw new Exception();
        }

        $this->pdo->commit();
    }
}
