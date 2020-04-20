<?php

declare(strict_types=1);

namespace App\Users\Services;

use App\Persistence\Users\UserPasswordResetTokenRecord;
use App\Users\Models\UserModel;
use PDO;

class FetchTotalUserResetTokens
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(UserModel $user) : int
    {
        $statement = $this->pdo->prepare(
            'SELECT COUNT(*) FROM ' .
                (new UserPasswordResetTokenRecord())->getTableName() .
            ' WHERE user_id = :id'
        );

        $statement->execute(['id' => $user->id]);

        $result = $statement->fetch();

        return (int) $result['count'];
    }
}
