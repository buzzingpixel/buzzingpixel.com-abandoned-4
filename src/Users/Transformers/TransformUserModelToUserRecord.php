<?php

declare(strict_types=1);

namespace App\Users\Transformers;

use App\Persistence\Users\UserRecord;
use App\Users\Models\UserModel;
use DateTimeInterface;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformUserModelToUserRecord
{
    public function __invoke(UserModel $userModel) : UserRecord
    {
        $userRecord = new UserRecord();

        $userRecord->id = $userModel->id;

        $userRecord->is_admin = $userModel->isAdmin ? '1' : '0';

        $userRecord->email_address = $userModel->emailAddress;

        $userRecord->password_hash = $userModel->passwordHash;

        $userRecord->is_active = $userModel->isActive ? '1' : '0';

        $userRecord->timezone = $userModel->timezone->getName();

        $userRecord->first_name = $userModel->firstName;

        $userRecord->last_name = $userModel->lastName;

        $userRecord->display_name = $userModel->displayName;

        $userRecord->billing_name = $userModel->billingName;

        $userRecord->billing_company = $userModel->billingCompany;

        $userRecord->billing_phone = $userModel->billingPhone;

        $userRecord->billing_country = $userModel->billingCountry;

        $userRecord->billing_address = $userModel->billingAddress;

        $userRecord->billing_city = $userModel->billingCity;

        $userRecord->billing_state_abbr = $userModel->billingStateAbbr;

        $userRecord->billing_postal_code = $userModel->billingPostalCode;

        $userRecord->created_at = $userModel->createdAt->format(
            DateTimeInterface::ATOM
        );

        return $userRecord;
    }
}
