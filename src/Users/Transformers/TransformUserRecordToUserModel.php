<?php

declare(strict_types=1);

namespace App\Users\Transformers;

use App\Persistence\Constants;
use App\Persistence\Users\UserRecord;
use App\Users\Models\UserModel;
use DateTimeImmutable;
use DateTimeZone;
use function assert;
use function in_array;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformUserRecordToUserModel
{
    public function __invoke(UserRecord $userRecord) : UserModel
    {
        $userModel = new UserModel();

        $userModel->id = $userRecord->id;

        $userModel->isAdmin = in_array(
            $userRecord->is_admin,
            ['1', 1, true],
            true,
        );

        $userModel->emailAddress = $userRecord->email_address;

        $userModel->isActive = in_array(
            $userRecord->is_active,
            ['1', 1, true],
            true,
        );

        $userModel->timezone = new DateTimeZone(
            $userRecord->timezone
        );

        $userModel->firstName = $userRecord->first_name;

        $userModel->lastName = $userRecord->last_name;

        $userModel->displayName = $userRecord->display_name;

        $userModel->billingName = $userRecord->billing_name;

        $userModel->billingCompany = $userRecord->billing_company;

        $userModel->billingPhone = $userRecord->billing_phone;

        $userModel->billingCountry = $userRecord->billing_country;

        $userModel->billingAddress = $userRecord->billing_address;

        $userModel->billingCity = $userRecord->billing_city;

        $userModel->billingStateAbbr = $userRecord->billing_state_abbr;

        $userModel->billingPostalCode = $userRecord->billing_postal_code;

        $createdAt = DateTimeImmutable::createFromFormat(
            Constants::POSTGRES_OUTPUT_FORMAT,
            $userRecord->created_at
        );

        assert($createdAt instanceof DateTimeImmutable);

        $userModel->createdAt = $createdAt;

        return $userModel;
    }
}
