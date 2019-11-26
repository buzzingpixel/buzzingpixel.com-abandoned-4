<?php

declare(strict_types=1);

namespace App\Users\Transformers;

use App\Persistence\Constants;
use App\Persistence\Users\UserRecord;
use App\Users\Models\UserModel;
use DateTimeImmutable;
use function in_array;

class TransformUserRecordToUserModel
{
    public function __invoke(UserRecord $userRecord) : UserModel
    {
        $createdAt = DateTimeImmutable::createFromFormat(
            Constants::POSTGRES_OUTPUT_FORMAT,
            $userRecord->created_at
        );

        return new UserModel([
            'id' => $userRecord->id,
            'isAdmin' => in_array($userRecord->is_admin, ['1', 1, true], true),
            'emailAddress' => $userRecord->email_address,
            'passwordHash' => $userRecord->password_hash,
            'isActive' => in_array($userRecord->is_active, ['1', 1, true], true),
            'firstName' => $userRecord->first_name,
            'lastName' => $userRecord->last_name,
            'displayName' => $userRecord->display_name,
            'billingName' => $userRecord->billing_name,
            'billingCompany' => $userRecord->billing_company,
            'billingPhone' => $userRecord->billing_phone,
            'billingCountry' => $userRecord->billing_country,
            'billingAddress' => $userRecord->billing_address,
            'billingCity' => $userRecord->billing_city,
            'billingPostalCode' => $userRecord->billing_postal_code,
            'createdAt' => $createdAt,
        ]);
    }
}
