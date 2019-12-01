<?php

declare(strict_types=1);

namespace App\Users\Services;

use App\Persistence\Users\UserPasswordResetTokenRecord;
use App\Users\Models\UserModel;
use PDO;
use Throwable;
use function is_bool;

class FetchUserByResetToken
{
    /** @var PDO */
    private $pdo;
    /** @var FetchUserById */
    private $fetchUserById;

    public function __construct(PDO $pdo, FetchUserById $fetchUserById)
    {
        $this->pdo           = $pdo;
        $this->fetchUserById = $fetchUserById;
    }

    public function __invoke(string $token) : ?UserModel
    {
        try {
            $statement = $this->pdo->prepare(
                'SELECT * FROM user_password_reset_tokens WHERE id=:id'
            );

            $statement->execute([':id' => $token]);

            /** @var UserPasswordResetTokenRecord|bool $record */
            $record = $statement->fetchObject(
                UserPasswordResetTokenRecord::class
            );

            if (is_bool($record)) {
                return null;
            }

            return ($this->fetchUserById)($record->user_id);
        } catch (Throwable $e) {
            return null;
        }
    }
}
