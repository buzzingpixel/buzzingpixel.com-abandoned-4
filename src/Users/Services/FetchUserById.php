<?php

declare(strict_types=1);

namespace App\Users\Services;

use App\Persistence\Users\UserRecord;
use App\Users\Models\UserModel;
use App\Users\Transformers\TransformUserRecordToUserModel;
use PDO;
use Throwable;

class FetchUserById
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

    public function __invoke(string $id) : ?UserModel
    {
        try {
            $query = $this->pdo->prepare(
                'SELECT * FROM users WHERE id = :id'
            );

            $query->execute([':id' => $id]);

            /** @var UserRecord|null $userRecord */
            $userRecord = $query->fetchObject(UserRecord::class);

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
