<?php

declare(strict_types=1);

namespace App\Users\Transformers;

use App\Persistence\Users\UserRecord;
use App\Users\Models\UserModel;
use DateTimeInterface;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformUserModelToUserRecord
{
    public function __invoke(UserModel $model): UserRecord
    {
        $record = new UserRecord();

        $record->id = $model->id;

        $record->is_admin = $model->isAdmin ? '1' : '0';

        $record->email_address = $model->emailAddress;

        $record->password_hash = $model->passwordHash;

        $record->is_active = $model->isActive ? '1' : '0';

        $record->timezone = $model->timezone->getName();

        $record->first_name = $model->firstName;

        $record->last_name = $model->lastName;

        $record->display_name = $model->displayName;

        $record->billing_name = $model->billingName;

        $record->billing_company = $model->billingCompany;

        $record->billing_phone = $model->billingPhone;

        $record->billing_country = $model->billingCountry;

        $record->billing_address = $model->billingAddress;

        $record->billing_city = $model->billingCity;

        $record->billing_state_abbr = $model->billingStateAbbr;

        $record->billing_postal_code = $model->billingPostalCode;

        $record->created_at = $model->createdAt->format(
            DateTimeInterface::ATOM
        );

        $record->stripe_id = $model->stripeId;

        return $record;
    }
}
