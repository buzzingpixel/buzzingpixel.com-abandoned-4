<?php

declare(strict_types=1);

namespace App\Users\Services;

use App\Persistence\Constants;
use App\Persistence\SaveExistingRecord;
use App\Persistence\Users\UserSessionRecord;
use App\Users\Models\UserModel;
use buzzingpixel\cookieapi\interfaces\CookieApiInterface;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use PDO;
use function is_bool;
use function time;

class FetchLoggedInUser
{
    /** @var CookieApiInterface */
    private $cookieApi;
    /** @var PDO */
    private $pdo;
    /** @var SaveExistingRecord */
    private $saveExistingRecord;
    /** @var FetchUserById */
    private $fetchUserById;

    public function __construct(
        CookieApiInterface $cookieApi,
        PDO $pdo,
        SaveExistingRecord $saveExistingRecord,
        FetchUserById $fetchUserById
    ) {
        $this->cookieApi          = $cookieApi;
        $this->pdo                = $pdo;
        $this->saveExistingRecord = $saveExistingRecord;
        $this->fetchUserById      = $fetchUserById;
    }

    public function __invoke() : ?UserModel
    {
        $cookie = $this->cookieApi->retrieveCookie('user_session_token');

        if ($cookie === null) {
            return null;
        }

        $statement = $this->pdo->prepare(
            'SELECT * FROM user_sessions WHERE id=:id'
        );

        $statement->execute([':id' => $cookie->value()]);

        /** @var UserSessionRecord|bool $sessionRecord */
        $sessionRecord = $statement->fetchObject(
            UserSessionRecord::class
        );

        if (is_bool($sessionRecord)) {
            $this->cookieApi->deleteCookie($cookie);

            return null;
        }

        /** @var DateTimeImmutable $lastTouchedAt */
        $lastTouchedAt = DateTimeImmutable::createFromFormat(
            Constants::POSTGRES_OUTPUT_FORMAT,
            $sessionRecord->last_touched_at
        );

        /**
         * We don't want to touch the session (write to the database) every time
         * we fetch the current user. So we'll only do it once every 24 hours
         */
        $h24 = 86400;

        $diff = time() - $lastTouchedAt->getTimestamp();

        if ($diff > $h24) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $dateTime = new DateTimeImmutable(
                'now',
                new DateTimeZone('UTC')
            );

            $sessionRecord->last_touched_at = $dateTime->format(
                DateTimeInterface::ATOM
            );

            ($this->saveExistingRecord)($sessionRecord);
        }

        return ($this->fetchUserById)($sessionRecord->user_id);
    }
}