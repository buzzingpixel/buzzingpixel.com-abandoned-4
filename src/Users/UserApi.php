<?php

declare(strict_types=1);

namespace App\Users;

use App\Payload\Payload;
use App\Users\Models\UserModel;
use App\Users\Services\FetchUserByEmailAddress;
use App\Users\Services\FetchUserById;
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
}
