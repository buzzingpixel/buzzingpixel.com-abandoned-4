<?php

declare(strict_types=1);

namespace App\Users\Models;

use App\Payload\Model;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

class UserModel extends Model
{
    /**
     * @inheritDoc
     */
    public function __construct(array $vars = [])
    {
        parent::__construct($vars);

        /** @psalm-suppress UninitializedProperty */
        $timeZoneInstance = $this->timezone instanceof DateTimeZone;

        if (! $timeZoneInstance) {
            $this->timezone = new DateTimeZone('America/Chicago');
        }

        /** @psalm-suppress UninitializedProperty */
        $createdAtInstance = $this->createdAt instanceof DateTimeImmutable;

        if ($createdAtInstance) {
            return;
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->createdAt = new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC')
        );
    }

    /** @var string */
    private $id = '';

    public function setId(string $id) : UserModel
    {
        $this->id = $id;

        return $this;
    }

    public function getId() : string
    {
        return $this->id;
    }

    /** @var bool */
    private $isAdmin = false;

    public function setIsAdmin(bool $isAdmin) : UserModel
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    public function isAdmin() : bool
    {
        return $this->isAdmin;
    }

    /** @var string */
    private $emailAddress = '';

    public function setEmailAddress(string $emailAddress) : UserModel
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    public function getEmailAddress() : string
    {
        return $this->emailAddress;
    }

    /** @var string */
    private $passwordHash = '';

    public function setPasswordHash(string $passwordHash) : UserModel
    {
        $this->passwordHash = $passwordHash;

        return $this;
    }

    public function getPasswordHash() : string
    {
        return $this->passwordHash;
    }

    /** @var string */
    private $newPassword = '';

    public function setNewPassword(string $newPassword) : UserModel
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getNewPassword() : string
    {
        return $this->newPassword;
    }

    /** @var bool */
    private $isActive = true;

    public function setIsActive(bool $isActive) : UserModel
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function isActive() : bool
    {
        return $this->isActive;
    }

    /**
     * @var DateTimeZone
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $timezone;

    public function setTimezone(DateTimeZone $timezone) : UserModel
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getTimezone() : DateTimeZone
    {
        return $this->timezone;
    }

    /** @var string */
    private $firstName = '';

    public function setFirstName(string $firstName) : UserModel
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getFirstName() : string
    {
        return $this->firstName;
    }

    /** @var string $lastName */
    private $lastName = '';

    public function setLastName(string $lastName) : UserModel
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getLastName() : string
    {
        return $this->lastName;
    }

    /** @var string */
    private $displayName = '';

    public function setDisplayName(string $displayName) : UserModel
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getDisplayName() : string
    {
        return $this->displayName;
    }

    /** @var string */
    private $billingName = '';

    public function setBillingName(string $billingName) : UserModel
    {
        $this->billingName = $billingName;

        return $this;
    }

    public function getBillingName() : string
    {
        return $this->billingName;
    }

    /** @var string */
    private $billingCompany = '';

    public function setBillingCompany(string $billingCompany) : UserModel
    {
        $this->billingCompany = $billingCompany;

        return $this;
    }

    public function getBillingCompany() : string
    {
        return $this->billingCompany;
    }

    /** @var string */
    private $billingPhone = '';

    public function setBillingPhone(string $billingPhone) : UserModel
    {
        $this->billingPhone = $billingPhone;

        return $this;
    }

    public function getBillingPhone() : string
    {
        return $this->billingPhone;
    }

    /** @var string */
    private $billingCountry = '';

    public function setBillingCountry(string $billingCountry) : UserModel
    {
        $this->billingCountry = $billingCountry;

        return $this;
    }

    public function getBillingCountry() : string
    {
        return $this->billingCountry;
    }

    /** @var string */
    private $billingAddress = '';

    public function setBillingAddress(string $billingAddress) : UserModel
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function getBillingAddress() : string
    {
        return $this->billingAddress;
    }

    /** @var string */
    private $billingCity = '';

    public function setBillingCity(string $billingCity) : UserModel
    {
        $this->billingCity = $billingCity;

        return $this;
    }

    public function getBillingCity() : string
    {
        return $this->billingCity;
    }

    /** @var string */
    private $billingPostalCode = '';

    public function setBillingPostalCode(string $billingPostalCode) : UserModel
    {
        $this->billingPostalCode = $billingPostalCode;

        return $this;
    }

    public function getBillingPostalCode() : string
    {
        return $this->billingPostalCode;
    }

    /**
     * @var DateTimeImmutable
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $createdAt;

    protected function setCreatedAt(DateTimeImmutable $createdAt) : void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt() : DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return mixed[]
     */
    public function asArray(bool $excludeId = true) : array
    {
        $array = [];

        if (! $excludeId) {
            $array['id'] = $this->getId();
        }

        $array['isAdmin'] = $this->isAdmin();

        $array['emailAddress'] = $this->getEmailAddress();

        // Lets not put this in the array for now
        // $array['passwordHash'] = $this->getPasswordHash();

        $array['isActive'] = $this->isActive();

        $array['timezone'] = $this->getTimezone()->getName();

        $array['firstName'] = $this->getFirstName();

        $array['lastName'] = $this->getLastName();

        $array['displayName'] = $this->getDisplayName();

        $array['billingName'] = $this->getBillingName();

        $array['billingCompany'] = $this->getBillingCompany();

        $array['billingPhone'] = $this->getBillingPhone();

        $array['billingCountry'] = $this->getBillingCountry();

        $array['billingAddress'] = $this->getBillingAddress();

        $array['billingCity'] = $this->getBillingCity();

        $array['billingPostalCode'] = $this->getBillingPostalCode();

        $array['createdAt'] = $this->getCreatedAt()->format(
            DateTimeInterface::ATOM
        );

        return $array;
    }
}
