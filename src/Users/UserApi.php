<?php

declare(strict_types=1);

namespace App\Users;

use App\Payload\Payload;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use App\Users\Services\DeleteUser;
use App\Users\Services\DeleteUserCard;
use App\Users\Services\FetchLoggedInUser;
use App\Users\Services\FetchTotalUserResetTokens;
use App\Users\Services\FetchTotalUsers;
use App\Users\Services\FetchUserByEmailAddress;
use App\Users\Services\FetchUserById;
use App\Users\Services\FetchUserByResetToken;
use App\Users\Services\FetchUserCardById;
use App\Users\Services\FetchUserCards;
use App\Users\Services\FetchUsersByLimitOffset;
use App\Users\Services\FetchUsersBySearch;
use App\Users\Services\GeneratePasswordResetToken;
use App\Users\Services\LogCurrentUserOut;
use App\Users\Services\LogUserIn;
use App\Users\Services\PostalCodeService;
use App\Users\Services\RequestPasswordResetEmail;
use App\Users\Services\ResetPasswordByToken;
use App\Users\Services\SaveUser;
use App\Users\Services\SaveUserCard;
use App\Users\Services\ValidateUserPassword;
use Psr\Container\ContainerInterface;
use Throwable;

use function assert;

class UserApi
{
    private ContainerInterface $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function saveUser(UserModel $userModel): Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(SaveUser::class);

        assert($service instanceof SaveUser);

        return $service($userModel);
    }

    public function fetchUserByEmailAddress(string $emailAddress): ?UserModel
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchUserByEmailAddress::class);

        assert($service instanceof FetchUserByEmailAddress);

        return $service($emailAddress);
    }

    public function fetchUserById(string $id): ?UserModel
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchUserById::class);

        assert($service instanceof FetchUserById);

        return $service($id);
    }

    /**
     * @param bool $rehashPasswordIfNeeded Only set false if about to update password
     */
    public function validateUserPassword(
        UserModel $user,
        string $password,
        bool $rehashPasswordIfNeeded = true
    ): bool {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(ValidateUserPassword::class);

        assert($service instanceof ValidateUserPassword);

        return $service(
            $user,
            $password,
            $rehashPasswordIfNeeded
        );
    }

    public function logUserIn(UserModel $user, string $password): Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(LogUserIn::class);

        assert($service instanceof LogUserIn);

        return $service($user, $password);
    }

    public function deleteUser(UserModel $user): Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(DeleteUser::class);

        assert($service instanceof DeleteUser);

        return $service($user);
    }

    public function fetchLoggedInUser(): ?UserModel
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchLoggedInUser::class);

        assert($service instanceof FetchLoggedInUser);

        return $service();
    }

    public function generatePasswordResetToken(UserModel $user): Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(GeneratePasswordResetToken::class);

        assert($service instanceof GeneratePasswordResetToken);

        return $service($user);
    }

    /**
     * @throws Throwable
     */
    public function requestPasswordResetEmail(UserModel $user): void
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(RequestPasswordResetEmail::class);

        assert($service instanceof RequestPasswordResetEmail);

        $service($user);
    }

    public function fetchTotalUserResetTokens(UserModel $user): int
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchTotalUserResetTokens::class);

        assert($service instanceof FetchTotalUserResetTokens);

        return $service($user);
    }

    public function fetchUserByResetToken(string $token): ?UserModel
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchUserByResetToken::class);

        assert($service instanceof FetchUserByResetToken);

        return $service($token);
    }

    public function logCurrentUserOut(): Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(LogCurrentUserOut::class);

        assert($service instanceof LogCurrentUserOut);

        return $service();
    }

    public function resetPasswordByToken(
        string $token,
        string $newPassword
    ): Payload {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(ResetPasswordByToken::class);

        assert($service instanceof ResetPasswordByToken);

        return $service($token, $newPassword);
    }

    public function fetchTotalUsers(): int
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchTotalUsers::class);

        assert($service instanceof FetchTotalUsers);

        return $service();
    }

    /**
     * @return UserModel[]
     */
    public function fetchUsersByLimitOffset(
        ?int $limit = null,
        int $offset = 0
    ): array {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchUsersByLimitOffset::class);

        assert($service instanceof FetchUsersByLimitOffset);

        return $service($limit, $offset);
    }

    /**
     * @return UserModel[]
     */
    public function fetchUsersBySearch(
        string $query,
        ?int $limit = null,
        int $offset = 0
    ): array {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchUsersBySearch::class);

        assert($service instanceof FetchUsersBySearch);

        return $service($query, $limit, $offset);
    }

    public function validatePostalCode(
        string $postalCode,
        string $alpha2Country
    ): bool {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(PostalCodeService::class);

        assert($service instanceof PostalCodeService);

        return $service->validatePostalCode(
            $postalCode,
            $alpha2Country
        );
    }

    public function fillModelFromPostalCode(UserModel $model): void
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(PostalCodeService::class);

        assert($service instanceof PostalCodeService);

        $service->fillModelFromPostalCode($model);
    }

    public function fillCardModelFromPostalCode(UserCardModel $model): void
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(PostalCodeService::class);

        assert($service instanceof PostalCodeService);

        $service->fillCardModelFromPostalCode($model);
    }

    public function saveUserCard(UserCardModel $userCard): Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(SaveUserCard::class);

        assert($service instanceof SaveUserCard);

        return $service($userCard);
    }

    /**
     * @return UserCardModel[]
     */
    public function fetchUserCards(UserModel $user): array
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchUserCards::class);

        assert($service instanceof FetchUserCards);

        return $service($user);
    }

    public function fetchUserCardById(
        UserModel $user,
        string $id
    ): ?UserCardModel {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchUserCardById::class);

        assert($service instanceof FetchUserCardById);

        return $service($user, $id);
    }

    public function deleteUserCard(UserCardModel $card): Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(DeleteUserCard::class);

        assert($service instanceof DeleteUserCard);

        return $service($card);
    }
}
