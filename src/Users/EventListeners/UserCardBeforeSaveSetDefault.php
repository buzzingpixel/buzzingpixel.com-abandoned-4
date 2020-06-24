<?php

declare(strict_types=1);

namespace App\Users\EventListeners;

use App\Persistence\RecordQueryFactory;
use App\Persistence\UserCards\UserCardRecord;
use App\Users\Events\SaveUserCardBeforeSave;
use App\Users\Models\UserCardModel;
use PDO;

class UserCardBeforeSaveSetDefault
{
    private PDO $pdo;
    private RecordQueryFactory $queryFactory;

    public function __construct(
        PDO $pdo,
        RecordQueryFactory $queryFactory
    ) {
        $this->pdo          = $pdo;
        $this->queryFactory = $queryFactory;
    }

    public function onBeforeSaveUserCard(
        SaveUserCardBeforeSave $beforeSave
    ): void {
        $model = $beforeSave->userCardModel;

        if ($model->isDefault) {
            $this->processAsDefaultCard($model);

            return;
        }

        $this->processAsNotDefaultCard($model);
    }

    private function processAsDefaultCard(UserCardModel $model): void
    {
        $t = UserCardRecord::tableName();

        $statement = $this->pdo->prepare(
            'UPDATE ' . $t . ' SET is_default = false WHERE id != :id'
        );

        $statement->execute([
            ':id' => $model->id,
        ]);
    }

    private function processAsNotDefaultCard(UserCardModel $model): void
    {
        // Find out if there is a default card
        $query = ($this->queryFactory)(new UserCardRecord())
            ->withWhere('is_default', '1')
            ->withWhere('user_id', $model->user->id)
            ->one();

        // If there is a default card, no action is necessary
        if ($query !== null) {
            return;
        }

        // Because there's no default card, make this one the default
        $model->isDefault = true;
    }
}
