<?php

declare(strict_types=1);

namespace App\Users;

use App\Payload\Payload;
use App\Users\Models\UserModel;
use App\Users\Services\DeleteUser;
use App\Users\Services\FetchLoggedInUser;
use App\Users\Services\FetchUserByEmailAddress;
use App\Users\Services\FetchUserById;
use App\Users\Services\GeneratePasswordResetToken;
use App\Users\Services\LogUserIn;
use App\Users\Services\SaveUser;
use Psr\Container\ContainerInterface;

class UserApi
{
    /** @var ContainerInterface */
    private $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function saveUser(UserModel $userModel) : Payload
    {
        /** @var SaveUser $service */
        $service = $this->di->get(SaveUser::class);

        return $service($userModel);
    }

    public function fetchUserByEmailAddress(string $emailAddress) : ?UserModel
    {
        /** @var FetchUserByEmailAddress $service */
        $service = $this->di->get(FetchUserByEmailAddress::class);

        return $service($emailAddress);
    }

    public function fetchUserById(string $id) : ?UserModel
    {
        /** @var FetchUserById $service */
        $service = $this->di->get(FetchUserById::class);

        return $service($id);
    }

    public function logUserIn(UserModel $user, string $password) : Payload
    {
        /** @var LogUserIn $service */
        $service = $this->di->get(LogUserIn::class);

        return $service($user, $password);
    }

    public function deleteUser(UserModel $user) : Payload
    {
        /** @var DeleteUser $service */
        $service = $this->di->get(DeleteUser::class);

        return $service($user);
    }

    public function fetchLoggedInUser() : ?UserModel
    {
        /** @var FetchLoggedInUser $service */
        $service = $this->di->get(FetchLoggedInUser::class);

        return $service();
    }

    public function generatePasswordResetToken(UserModel $user) : Payload
    {
        /** @var GeneratePasswordResetToken $service */
        $service = $this->di->get(GeneratePasswordResetToken::class);

        return $service($user);
    }
}
