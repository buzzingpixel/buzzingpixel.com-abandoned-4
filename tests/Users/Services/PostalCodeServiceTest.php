<?php

declare(strict_types=1);

namespace Tests\Users\Services;

use App\Users\Models\UserModel;
use App\Users\Services\PostalCodeService;
use PHPUnit\Framework\TestCase;
use Tests\TestConfig;

class PostalCodeServiceTest extends TestCase
{
    public function testValidatePostalCodeWithInvalidInput() : void
    {
        $postalCodeService = TestConfig::$di->get(PostalCodeService::class);

        self::assertFalse($postalCodeService->validatePostalCode(
            'adsf',
            'asdf'
        ));

        self::assertFalse($postalCodeService->validatePostalCode(
            'adsf',
            'asdf'
        ));
    }

    public function testValidatePostalCode() : void
    {
        $postalCodeService = TestConfig::$di->get(PostalCodeService::class);

        self::assertTrue($postalCodeService->validatePostalCode(
            '37174',
            'US'
        ));

        self::assertTrue($postalCodeService->validatePostalCode(
            '37174',
            'US'
        ));
    }

    public function testFillModelFromPostalCodeInvalid() : void
    {
        $userModel = new UserModel();

        $userModel->billingPostalCode = 'adsf';

        $userModel->billingCountry = 'adsf';

        $postalCodeService = TestConfig::$di->get(PostalCodeService::class);

        $postalCodeService->fillModelFromPostalCode($userModel);

        self::assertSame(
            '',
            $userModel->billingCity
        );

        self::assertSame(
            '',
            $userModel->billingStateAbbr
        );
    }

    public function testFillModelFromPostalCode() : void
    {
        $userModel = new UserModel();

        $userModel->billingPostalCode = '37174';

        $userModel->billingCountry = 'US';

        $postalCodeService = TestConfig::$di->get(PostalCodeService::class);

        $postalCodeService->fillModelFromPostalCode($userModel);

        self::assertSame(
            'Spring Hill',
            $userModel->billingCity
        );

        self::assertSame(
            'TN',
            $userModel->billingStateAbbr
        );
    }
}
