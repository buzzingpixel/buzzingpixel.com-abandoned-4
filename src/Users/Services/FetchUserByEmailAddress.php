<?php

declare(strict_types=1);

namespace App\Users\Services;

use App\Persistence\Users\UserRecord;
use App\Users\Models\UserModel;
use App\Users\Transformers\TransformUserRecordToUserModel;
use PDO;
use Throwable;

class FetchUserByEmailAddress
{
    /** @var PDO */
    private $pdo;
    /** @var TransformUserRecordToUserModel */
    private $transformUserRecordToUserModel;

    public function __construct(
        PDO $pdo,
        TransformUserRecordToUserModel $transformUserRecordToUserModel
    ) {
        $this->pdo                            = $pdo;
        $this->transformUserRecordToUserModel = $transformUserRecordToUserModel;
    }

    public function __invoke(string $emailAddress) : ?UserModel
    {
        try {
            $statement = $this->pdo->prepare(
                'SELECT * FROM users WHERE email_address = :email'
            );

            $statement->execute([':email' => $emailAddress]);

            /** @var UserRecord|bool $userRecord */
            $userRecord = $statement->fetchObject(UserRecord::class);

            $isInstance = $userRecord instanceof UserRecord;

            if ($userRecord === null || ! $isInstance) {
                return null;
            }

            return ($this->transformUserRecordToUserModel)($userRecord);
        } catch (Throwable $e) {
            return null;
        }
    }
}
