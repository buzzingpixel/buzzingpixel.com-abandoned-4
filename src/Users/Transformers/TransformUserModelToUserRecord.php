<?php

declare(strict_types=1);

namespace App\Users\Transformers;

use App\Persistence\Users\UserRecord;
use App\Users\Models\UserModel;
use DateTimeInterface;

class TransformUserModelToUserRecord
{
    public function __invoke(UserModel $userModel) : UserRecord
    {
        $userRecord = new UserRecord();

        $userRecord->id = $userModel->getId();

        $userRecord->is_admin = $userModel->isAdmin() ? '1' : '0';

        $userRecord->email_address = $userModel->getEmailAddress();

        $userRecord->password_hash = $userModel->getPasswordHash();

        $userRecord->is_active = $userModel->isActive() ? '1' : '0';

        $userRecord->first_name = $userModel->getFirstName();

        $userRecord->last_name = $userModel->getLastName();

        $userRecord->display_name = $userModel->getDisplayName();

        $userRecord->billing_name = $userModel->getBillingName();

        $userRecord->billing_company = $userModel->getBillingCompany();

        $userRecord->billing_phone = $userModel->getBillingPhone();

        $userRecord->billing_country = $userModel->getBillingCountry();

        $userRecord->billing_address = $userModel->getBillingAddress();

        $userRecord->billing_city = $userModel->getBillingCity();

        $userRecord->billing_postal_code = $userModel->getBillingPostalCode();

        $userRecord->created_at = $userModel->getCreatedAt()->format(DateTimeInterface::ATOM);

        return $userRecord;
    }
}
