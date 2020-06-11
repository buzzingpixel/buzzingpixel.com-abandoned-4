<?php

declare(strict_types=1);

namespace App\Users\Models;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use function trim;

class UserModel
{
    public function __construct()
    {
        $this->timezone = new DateTimeZone('US/Central');

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->createdAt = new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC')
        );
    }

    public string $id = '';

    public bool $isAdmin = false;

    public string $emailAddress = '';

    public string $passwordHash = '';

    public string $newPassword = '';

    public bool $isActive = true;

    public DateTimeZone $timezone;

    public string $firstName = '';

    public string $lastName = '';

    public string $displayName = '';

    public string $billingName = '';

    public string $billingCompany = '';

    public string $billingPhone = '';

    public string $billingCountry = '';

    public string $billingAddress = '';

    public string $billingCity = '';

    public string $billingStateAbbr = '';

    public string $billingPostalCode = '';

    public DateTimeImmutable $createdAt;

    public string $stripeId = '';

    /**
     * @return mixed[]
     */
    public function asArray(bool $excludeId = true) : array
    {
        $array = [];

        if (! $excludeId) {
            $array['id'] = $this->id;
        }

        $array['isAdmin'] = $this->isAdmin;

        $array['emailAddress'] = $this->emailAddress;

        // Lets not put this in the array for now
        // $array['passwordHash'] = $this->passwordHash;

        $array['isActive'] = $this->isActive;

        $array['timezone'] = $this->timezone->getName();

        $array['firstName'] = $this->firstName;

        $array['lastName'] = $this->lastName;

        $array['displayName'] = $this->displayName;

        $array['billingName'] = $this->billingName;

        $array['billingCompany'] = $this->billingCompany;

        $array['billingPhone'] = $this->billingPhone;

        $array['billingCountry'] = $this->billingCountry;

        $array['billingAddress'] = $this->billingAddress;

        $array['billingCity'] = $this->billingCity;

        $array['billingPostalCode'] = $this->billingPostalCode;

        $array['createdAt'] = $this->createdAt->format(
            DateTimeInterface::ATOM
        );

        return $array;
    }

    public function getFullName() : string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }
}
