<?php

declare(strict_types=1);

namespace App\Users\Services;

use App\Payload\Payload;
use App\Persistence\SaveExistingRecord;
use App\Persistence\SaveNewRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use App\Users\Models\UserModel;
use App\Users\Transformers\TransformUserModelToUserRecord;
use Ramsey\Uuid\UuidFactoryInterface;
use const FILTER_VALIDATE_EMAIL;
use const PASSWORD_DEFAULT;
use function count;
use function filter_var;
use function mb_strlen;
use function password_hash;

class SaveUser
{
    public const MIN_PASSWORD_LENGTH = 8;

    /** @var FetchUserByEmailAddress */
    private $fetchUserByEmailAddress;
    /** @var SaveNewRecord */
    private $saveNewRecord;
    /** @var FetchUserById */
    private $fetchUserById;
    /** @var SaveExistingRecord */
    private $saveExistingRecord;
    /** @var TransformUserModelToUserRecord */
    private $transformUserModelToUserRecord;
    /** @var UuidFactoryInterface */
    private $uuidFactory;

    public function __construct(
        FetchUserByEmailAddress $fetchUserByEmailAddress,
        SaveNewRecord $saveNewRecord,
        FetchUserById $fetchUserById,
        SaveExistingRecord $saveExistingRecord,
        TransformUserModelToUserRecord $transformUserModelToUserRecord,
        UuidFactoryWithOrderedTimeCodec $uuidFactory
    ) {
        $this->fetchUserByEmailAddress        = $fetchUserByEmailAddress;
        $this->saveNewRecord                  = $saveNewRecord;
        $this->fetchUserById                  = $fetchUserById;
        $this->saveExistingRecord             = $saveExistingRecord;
        $this->transformUserModelToUserRecord = $transformUserModelToUserRecord;
        $this->uuidFactory                    = $uuidFactory;
    }

    public function __invoke(UserModel $userModel) : Payload
    {
        $errors = [];

        if ($userModel->getPasswordHash() === '' && $userModel->getNewPassword() === '') {
            $errors['password'] = 'Password is required';
        }

        if (filter_var($userModel->getEmailAddress(), FILTER_VALIDATE_EMAIL) === false) {
            $errors['emailAddress'] = 'A valid email address is required';
        }

        $newPass = $userModel->getNewPassword();

        if ($newPass !== '' && mb_strlen($newPass) < self::MIN_PASSWORD_LENGTH) {
            $errors['password'] = 'Password is too short';
        }

        if (count($errors) > 0) {
            return new Payload(Payload::STATUS_NOT_VALID, $errors);
        }

        if ($newPass !== '') {
            $userModel->setPasswordHash(
                (string) password_hash(
                    $newPass,
                    PASSWORD_DEFAULT
                )
            );
        }

        if ($userModel->getId() === '') {
            return $this->saveNewUser($userModel);
        }

        return $this->saveExistingUser($userModel);
    }

    private function saveNewUser(UserModel $userModel) : Payload
    {
        $email = $userModel->getEmailAddress();

        $existingUser = ($this->fetchUserByEmailAddress)($email);

        if ($existingUser !== null) {
            return new Payload(Payload::STATUS_NOT_CREATED, [
                'message' => 'User with email address ' . $email . ' already exists',
            ]);
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $userModel->setId($this->uuidFactory->uuid1()->toString());

        $userRecord = ($this->transformUserModelToUserRecord)($userModel);

        return ($this->saveNewRecord)($userRecord);
    }

    private function saveExistingUser(UserModel $userModel) : Payload
    {
        $existingUser = ($this->fetchUserById)($userModel->getId());

        if ($existingUser === null) {
            return new Payload(Payload::STATUS_NOT_FOUND, [
                'message' => 'User with id ' . $userModel->getId() . ' not found',
            ]);
        }

        $userRecord = ($this->transformUserModelToUserRecord)($userModel);

        return ($this->saveExistingRecord)($userRecord);
    }
}
