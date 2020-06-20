<?php

declare(strict_types=1);

namespace App\Users\Transformers;

use App\Persistence\Constants;
use App\Persistence\Users\UserRecord;
use App\Users\Models\UserModel;
use DateTimeZone;
use Safe\DateTimeImmutable;

use function in_array;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformUserRecordToUserModel
{
    public function __invoke(UserRecord $record): UserModel
    {
        $model = new UserModel();

        $model->id = $record->id;

        $model->isAdmin = in_array(
            $record->is_admin,
            ['1', 1, true],
            true,
        );

        $model->emailAddress = $record->email_address;

        $model->passwordHash = $record->password_hash;

        $model->isActive = in_array(
            $record->is_active,
            ['1', 1, true],
            true,
        );

        $model->timezone = new DateTimeZone(
            $record->timezone
        );

        $model->firstName = $record->first_name;

        $model->lastName = $record->last_name;

        $model->displayName = $record->display_name;

        $model->billingName = $record->billing_name;

        $model->billingCompany = $record->billing_company;

        $model->billingPhone = $record->billing_phone;

        $model->billingCountry = $record->billing_country;

        $model->billingAddress = $record->billing_address;

        $model->billingCity = $record->billing_city;

        $model->billingStateAbbr = $record->billing_state_abbr;

        $model->billingPostalCode = $record->billing_postal_code;

        $createdAt = DateTimeImmutable::createFromFormat(
            Constants::POSTGRES_OUTPUT_FORMAT,
            $record->created_at
        );

        $model->createdAt = $createdAt;

        $model->stripeId = $record->stripe_id;

        return $model;
    }
}
