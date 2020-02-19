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
use App\Users\Services\PostalCodeService;
use App\Users\Services\ResetPasswordByToken;
use App\Users\Services\SaveUser;
use Psr\Container\ContainerInterface;
use function assert;

class UserApi
{
    private ContainerInterface $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function saveUser(UserModel $userModel) : Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(SaveUser::class);
        assert($service instanceof SaveUser);

        return $service($userModel);
    }

    public function fetchUserByEmailAddress(string $emailAddress) : ?UserModel
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchUserByEmailAddress::class);
        assert($service instanceof FetchUserByEmailAddress);

        return $service($emailAddress);
    }

    public function fetchUserById(string $id) : ?UserModel
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchUserById::class);
        assert($service instanceof FetchUserById);

        return $service($id);
    }

    public function logUserIn(UserModel $user, string $password) : Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(LogUserIn::class);
        assert($service instanceof LogUserIn);

        return $service($user, $password);
    }

    public function deleteUser(UserModel $user) : Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(DeleteUser::class);
        assert($service instanceof DeleteUser);

        return $service($user);
    }

    public function fetchLoggedInUser() : ?UserModel
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchLoggedInUser::class);
        assert($service instanceof FetchLoggedInUser);

        return $service();
    }

    public function generatePasswordResetToken(UserModel $user) : Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(GeneratePasswordResetToken::class);
        assert($service instanceof GeneratePasswordResetToken);

        return $service($user);
    }

    public function fetchUserByResetToken(string $token) : ?UserModel
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchUserByResetToken::class);
        assert($service instanceof FetchUserByResetToken);

        return $service($token);
    }

    public function logCurrentUserOut() : Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(LogCurrentUserOut::class);
        assert($service instanceof LogCurrentUserOut);

        return $service();
    }

    public function resetPasswordByToken(string $token, string $newPassword) : Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(ResetPasswordByToken::class);
        assert($service instanceof ResetPasswordByToken);

        return $service($token, $newPassword);
    }

    public function fetchTotalUsers() : int
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchTotalUsers::class);
        assert($service instanceof FetchTotalUsers);

        return $service();
    }

    /**
     * @return UserModel[]
     */
    public function fetchUsersByLimitOffset(?int $limit = null, int $offset = 0) : array
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchUsersByLimitOffset::class);
        assert($service instanceof FetchUsersByLimitOffset);

        return $service($limit, $offset);
    }

    /**
     * @return UserModel[]
     */
    public function fetchUsersBySearch(string $query, ?int $limit = null, int $offset = 0) : array
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchUsersBySearch::class);
        assert($service instanceof FetchUsersBySearch);

        return $service($query, $limit, $offset);
    }

    public function validatePostalCode(string $postalCode, string $alpha2Country) : bool
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(PostalCodeService::class);
        assert($service instanceof PostalCodeService);

        return $service->validatePostalCode(
            $postalCode,
            $alpha2Country
        );
    }

    public function fillModelFromPostalCode(UserModel $model) : void
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(PostalCodeService::class);

        assert($service instanceof PostalCodeService);

        $service->fillModelFromPostalCode($model);
    }
}
