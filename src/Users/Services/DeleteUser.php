<?php

declare(strict_types=1);

namespace App\Users\Services;

use App\Payload\Payload;
use App\Users\Models\UserModel;
use PDO;
use Throwable;

class DeleteUser
{
    /** @var PDO */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(UserModel $user) : Payload
    {
        try {
            $this->pdo->beginTransaction();

            $this->deleteUser($user);

            $this->deleteUserSessions($user);

            $this->deletePasswordResetTokens($user);

            $this->pdo->commit();

            return new Payload(Payload::STATUS_SUCCESSFUL);
        } catch (Throwable $e) {
            $this->pdo->rollBack();

            return new Payload(
                Payload::STATUS_ERROR,
                ['message' => 'An unknown error occurred']
            );
        }
    }

    private function deleteUser(UserModel $user) : void
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM users WHERE guid=:id'
        );

        $statement->execute([':id' => $user->getId()]);
    }

    private function deleteUserSessions(UserModel $user) : void
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM user_sessions WHERE guid=:user_id'
        );

        $statement->execute([':user_id' => $user->getId()]);
    }

    private function deletePasswordResetTokens(UserModel $user) : void
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM user_password_reset_tokens WHERE guid=:user_id'
        );

        $statement->execute([':user_id' => $user->getId()]);
    }
}
