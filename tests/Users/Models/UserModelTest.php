<?php

declare(strict_types=1);

namespace Tests\Users\Models;

use App\Users\Models\UserModel;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase
{
    public function testAsArray() : void
    {
        $model = new UserModel();

        $model->id = 'foo-id';

        $model->isAdmin = true;

        $model->emailAddress = 'foo-email';

        $model->passwordHash = 'foo-password-hash';

        $model->isActive = false;

        $model->timezone = new DateTimeZone('America/New_York');

        $model->firstName = 'foo-first';

        $model->lastName = 'foo-last';

        $model->displayName = 'foo-display';

        $model->billingName = 'foo-billing-name';

        $model->billingCompany = 'foo-billing-company';

        $model->billingPhone = 'foo-billing-phone';

        $model->billingCountry = 'foo-billing-country';

        $model->billingAddress = 'foo-billing-address';

        $model->billingCity = 'foo-billing-city';

        $model->billingPostalCode = 'foo-billing-postal-code';

        $createdAt = new DateTimeImmutable();

        $model->createdAt = $createdAt;

        self::assertSame(
            [
                'isAdmin' => true,
                'emailAddress' => 'foo-email',
                'isActive' => false,
                'timezone' => 'America/New_York',
                'firstName' => 'foo-first',
                'lastName' => 'foo-last',
                'displayName' => 'foo-display',
                'billingName' => 'foo-billing-name',
                'billingCompany' => 'foo-billing-company',
                'billingPhone' => 'foo-billing-phone',
                'billingCountry' => 'foo-billing-country',
                'billingAddress' => 'foo-billing-address',
                'billingCity' => 'foo-billing-city',
                'billingPostalCode' => 'foo-billing-postal-code',
                'createdAt' => $createdAt->format(
                    DateTimeInterface::ATOM
                ),
            ],
            $model->asArray()
        );

        self::assertSame(
            [
                'id' => 'foo-id',
                'isAdmin' => true,
                'emailAddress' => 'foo-email',
                'isActive' => false,
                'timezone' => 'America/New_York',
                'firstName' => 'foo-first',
                'lastName' => 'foo-last',
                'displayName' => 'foo-display',
                'billingName' => 'foo-billing-name',
                'billingCompany' => 'foo-billing-company',
                'billingPhone' => 'foo-billing-phone',
                'billingCountry' => 'foo-billing-country',
                'billingAddress' => 'foo-billing-address',
                'billingCity' => 'foo-billing-city',
                'billingPostalCode' => 'foo-billing-postal-code',
                'createdAt' => $createdAt->format(
                    DateTimeInterface::ATOM
                ),
            ],
            $model->asArray(false)
        );
    }

    public function testGetFullName() : void
    {
        $model = new UserModel();

        $model->firstName = 'foo-first';

        $model->lastName = 'foo-last';

        self::assertSame(
            'foo-first foo-last',
            $model->getFullName(),
        );
    }
}
