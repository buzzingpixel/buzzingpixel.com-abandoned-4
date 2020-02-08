<?php

declare(strict_types=1);

namespace App\Users\Services;

use App\Payload\Payload;
use App\Users\Models\UserModel;
use buzzingpixel\cookieapi\interfaces\CookieApiInterface;
use DateTimeImmutable;
use DateTimeZone;
use function password_needs_rehash;
use function password_verify;
use function Safe\password_hash;
use function Safe\strtotime;
use const PASSWORD_DEFAULT;

class LogUserIn
{
    private SaveUser $saveUser;
    private CreateUserSession $createUserSession;
    private CookieApiInterface $cookieApi;

    public function __construct(
        SaveUser $saveUser,
        CreateUserSession $createUserSession,
        CookieApiInterface $cookieApi
    ) {
        $this->saveUser          = $saveUser;
        $this->createUserSession = $createUserSession;
        $this->cookieApi         = $cookieApi;
    }

    public function __invoke(UserModel $user, string $password) : Payload
    {
        $hash = $user->getPasswordHash();

        if (! password_verify($password, $hash)) {
            return new Payload(
                Payload::STATUS_NOT_VALID,
                ['message' => 'Your password is invalid']
            );
        }

        if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
            /**
             * @noinspection PhpUnhandledExceptionInspection, PhpStrictTypeCheckingInspection
             * @psalm-suppress NullArgument
             */
            $user->setPasswordHash(
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                )
            );

            ($this->saveUser)($user);
        }

        $createSessionPayload = ($this->createUserSession)($user);

        if ($createSessionPayload->getStatus() !== Payload::STATUS_CREATED) {
            return new Payload(Payload::STATUS_ERROR);
        }

        /** @var array<string, string> $result */
        $result = $createSessionPayload->getResult();

        $sessionId = $result['id'];

        /** @noinspection PhpUnhandledExceptionInspection */
        $currentDate = new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC')
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $currentDatePlus20Years = $currentDate->setTimestamp(
            strtotime('+ 20 years')
        );

        $this->cookieApi->saveCookie(
            $this->cookieApi->makeCookie(
                'user_session_token',
                $sessionId,
                $currentDatePlus20Years
            )
        );

        return new Payload(
            Payload::STATUS_SUCCESSFUL,
            ['id' => $sessionId]
        );
    }
}
