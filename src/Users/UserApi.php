<?php

declare(strict_types=1);

namespace App\Users;

use App\Payload\Payload;
use App\Users\Models\UserModel;
use App\Users\Services\DeleteUser;
use App\Users\Services\FetchLoggedInUser;
use App\Users\Services\FetchTotalUsers;
use App\Users\Services\FetchUserByEmailAddress;
use App\Users\Services\FetchUserById;
use App\Users\Services\FetchUserByResetToken;
use App\Users\Services\FetchUsersByLimitOffset;
use App\Users\Services\FetchUsersBySearch;
use App\Users\Services\GeneratePasswordResetToken;
use App\Users\Services\LogCurrentUserOut;
use App\Users\Services\LogUserIn;
use App\Users\Services\ResetPasswordByToken;
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

    public function fetchUserByResetToken(string $token) : ?UserModel
    {
        /** @var FetchUserByResetToken $service */
        $service = $this->di->get(FetchUserByResetToken::class);

        return $service($token);
    }

    public function logCurrentUserOut() : Payload
    {
        /** @var LogCurrentUserOut $service */
        $service = $this->di->get(LogCurrentUserOut::class);

        return $service();
    }

    public function resetPasswordByToken(string $token, string $newPassword) : Payload
    {
        /** @var ResetPasswordByToken $service */
        $service = $this->di->get(ResetPasswordByToken::class);

        return $service($token, $newPassword);
    }

    public function fetchTotalUsers() : int
    {
        /** @var FetchTotalUsers $service */
        $service = $this->di->get(FetchTotalUsers::class);

        return $service();
    }

    /**
     * @return UserModel[]
     */
    public function fetchUsersByLimitOffset(?int $limit = null, int $offset = 0) : array
    {
        /** @var FetchUsersByLimitOffset $service */
        $service = $this->di->get(FetchUsersByLimitOffset::class);

        return $service($limit, $offset);
    }

    /**
     * @return UserModel[]
     */
    public function fetchUsersBySearch(string $query, ?int $limit = null, int $offset = 0) : array
    {
        /** @var FetchUsersBySearch $service */
        $service = $this->di->get(FetchUsersBySearch::class);

        return $service($query, $limit, $offset);
    }
}
